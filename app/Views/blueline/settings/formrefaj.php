 <form class="form-horizontal" id="ajoutoccref"  method="post" id="ste_form">
                    <input type="text" hidden id="pr" name="idref"/> 
                    <div class="alert alert-error hide">
                              <button class="close" data-dismiss="alert"></button>
                              Erreur de saisie
                    </div>

                         
                    <div class="alert alert-success hide">
                              <button class="close" data-dismiss="alert"></button>
                              formulaire validé
                    </div>      
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-5">Libellé<span class="obligatoire"> *</span></label>
                                <div class="col-md-7">
                                 <input type="text" data-required="1" id="libellea" name="libellea" class="form-control input-medium" required>
                                 <span class="error_nom"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-5">Description<span class="obligatoire"> *</span></label>
                                <div class="col-md-7">
                                 <input type="text"    data-required="1" id="desca" name="desca" class="form-control input-large" required>
                             
                                </div>
                            </div>
                        </div>
                    </div>   
                    <div class="form-actions right">
                       <button type="button" id="fermer" class="btn red" data-dismiss="modal"><i class="fa fa-reply"></i> Fermer</button>
                       <button class="btn green" type="submit"><i class="fa fa-save"></i> Sauvegarder</button> 
                    </div>
                </form>