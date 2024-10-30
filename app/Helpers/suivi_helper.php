<?php

// Load necessary services and config
use Config\Services;
use Config\Database;

class HelperFunctions
{
    /**
     * Move the image from temporary to the final directory and delete old temp images.
     * 
     * @param string $temp_file
     * @param string $upload_file_delete
     */
    public function image_move($temp_file = false, $upload_file_delete = false)
    {
        $config = config('App'); // Load app config or your custom config file
        $imageOperator = Services::imageOperator(); // Load your custom image operator service



        if ($upload_file_delete) {
            $imageOperator->delete_upload($upload_file_delete); // Delete existing file if necessary
        }

        if ($temp_file) {
            $imageOperator->delete_temp($temp_file); // Clean up temp file
        }

        $imageOperator->delete_old_temp(); // Clear old cache/temp files
    }

    /**
     * Return the active class for menu items based on the current controller/method.
     * 
     * @param string $section
     * @return void
     */
    public function menu_active_class($section)
    {
        $router = service('router');
        $path = $router->controllerName() . '/' . $router->methodName();

        $sections = [
            'browse' => ['main/index'],
            'edit' => ['main/edit', 'main/edit_select'],
            'manage' => [
                'items/index',
                'items/edit',
                'item_types/index',
                'item_types/edit',
                'event_types/index',
                'event_types/edit',
                'users/index',
                'users/edit',
                'permissions/users',
                'permissions/user_edit',
                'permissions/groups',
                'permissions/group_edit',
                'settings/index'
            ]
        ];

        echo in_array($path, $sections[$section]) ? 'active' : '';
    }

    /**
     * Find the project name by querying the database based on user and date range.
     * 
     * @param string $status
     * @param int $id_user
     * @param string $date
     * @return array
     */
    public function find_project_name($status, $id_user, $date)
    {
        $db = Database::connect();
        $dates = strtotime($date);
        $newformat = date('Y-m-d', $dates);

        // SQL query to get the project name and ticket subject
        $sql = "SELECT projects.name, tickets.subject 
                FROM tickets
                INNER JOIN projects ON tickets.project_id = projects.id
                INNER JOIN users ON tickets.collaborater_id = users.id
                WHERE (users.salaries_id = ? AND (? BETWEEN tickets.start AND tickets.end))";

        // Execute the query
        $query = $db->query($sql, [$id_user, $newformat]);

        // Return the results
        return $query->getResultArray();
    }
}
