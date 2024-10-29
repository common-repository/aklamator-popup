
<div id="aklamator-options-pop" style="width:1160px;margin-top:10px;">
    <div class="left" style="float: left; width: 830px;">
        <div style="float: left; width: 300px;">

            <a target="_blank" href="<?php echo $this->aklamator_url; ?>?utm_source=wp-plugin">
                <img style="border-radius:5px;border:0px;" src="<?php echo POP_AKLA_PLUGIN_URL."images/logo.jpg";?>" /></a>
            <?php
            if ($this->application_id != '') : ?>
                <a target="_blank" href="<?php echo $this->aklamator_url; ?>dashboard?utm_source=wp-plugin">
                    <img style="border:0px;margin-top:5px;border-radius:5px;" src="<?php echo POP_AKLA_PLUGIN_URL."images/dashboard.jpg"; ?>" /></a>

            <?php endif; ?>

            <a target="_blank" href="<?php echo $this->aklamator_url;?>contact?utm_source=wp-plugin-contact">
                <img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;" src="<?php echo POP_AKLA_PLUGIN_URL."images/support.jpg"; ?>" /></a>

            <a target="_blank" href="http://qr.rs/q/4649f"><img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;" src="<?php echo POP_AKLA_PLUGIN_URL."images/promo-300x200.png"; ?>" /></a>

        </div>

        <div class="box">

            <h1>Aklamator PopUP Digital PR</h1>

            <?php

            if (isset($this->api_data->error) || $this->application_id == '') : ?>
                <h3 style="float: left">Step 1: Get your Aklamator Aplication ID</h3>
                <a class='aklamator_button aklamator-login-button' id="aklamator_login_button" >Click here for FREE registration/login</a>
                <div style="clear: both"></div>
                <p>Or you can manually <a href="<?php echo $this->aklamator_url . 'registration/publisher'; ?>" target="_blank">register</a> or <a href="<?php echo $this->aklamator_url . 'login'; ?>" target="_blank">login</a> and copy paste your Application ID</p>
                <script>var signup_url = '<?php echo $this->getSignupUrl(); ?>';</script>
            <?php endif; ?>



            <div style="clear: both"></div>
            <?php if ($this->application_id == '') { ?>
                <h3>Step 2: &nbsp;&nbsp;&nbsp;&nbsp; Paste your Aklamator Application ID</h3>
            <?php }else{ ?>
                <h3>Your Aklamator Application ID</h3>
            <?php } ?>


            <form method="post" action="options.php">
                <?php
                settings_fields('aklamatorPop-options');

                ?>

                <p >
                    <input type="text" style="width: 400px" name="aklamatorPopApplicationID" id="aklamatorPopApplicationID" value="<?php echo $this->application_id;?>" maxlength="999" onchange="appIDChange(this.value)"/>

                </p>
                <p>
                    <input type="checkbox" id="aklamatorPopPoweredBy" name="aklamatorPopPoweredBy" <?php echo (get_option("aklamatorPopPoweredBy") == true ? 'checked="checked"' : ''); ?> Required="Required">
                    <strong>Required</strong> I acknowledge there is a 'powered by aklamator' link on the widget. <br />
                </p>
                <p>
                    <input type="checkbox" id="aklamatorPopFeatured2Feed" name="aklamatorPopFeatured2Feed" <?php echo (get_option("aklamatorPopFeatured2Feed") == true ? 'checked="checked"' : ''); ?> >
                    <strong>Add featured</strong> images from posts to your site's RSS feed output
                </p>

                <p>
                <div class="alert alert-msg">
                    <strong>Note </strong><span style="color: red">*</span>: By default, posts without images will not be shown in widgets. If you want to show them click on <strong>EDIT</strong> in table below!
                </div>
                </p>

                <?php if(isset($this->api_data->flag) && $this->api_data->flag === false): ?>
                    <p id="aklamator_error" class="alert_red alert-msg_red"><span style="color:red"><?php echo $this->api_data->error; ?></span></p>
                <?php endif; ?>
                


                <?php
                $display = 'style="display: none"';
                if($this->application_id !='' && $this->api_data->flag){
                    $display = "";

                    $widgets = $this->api_data->data;

                    /* Add new item to the end of array */
                    $item_add = new stdClass();
                    $item_add->uniq_name = 'none';
                    $item_add->title = 'Do not show';
                    $widgets[] = $item_add;

                    $classic = $this->api_data->classic;
                    /* Add new item to the end of array */
                    $item_add1 = new stdClass();
                    $item_add1->uniq_name = 'none';
                    $item_add1->title = 'Do not show';
                    $classic[] = $item_add1;

                }

                ?>
                <div <?php echo $display;?>>
                    <p>
                    <h1>PopUp Options</h1>
                    <h4>Select widget to be shown on PopUp window:</h4>
                    <p>
                        <label for="aklamatorPopUpTitle">PopUp Title: </label>
                        <input type="text" style="width: 300px; margin-bottom:10px" name="aklamatorPopUpTitle" id="aklamatorPopUpTitle" value="<?php echo get_option("aklamatorPopUpTitle"); ?>" maxlength="999" />
                    </p>
                    <p>
                        <label for="aklamatorPopUpSubTitle">PopUp Sub-Title: </label>
                        <input type="text" style="width: 300px; margin-bottom:10px" name="aklamatorPopUpSubTitle" id="aklamatorPopUpSubTitle" value="<?php echo get_option("aklamatorPopUpSubTitle"); ?>" maxlength="999" />
                    </p>
                    <p>
                        <label for="aklamatorPopUpWidgetID">Select PopUp widget: </label>
                        <select id="aklamatorPopUpWidgetID" name="aklamatorPopUpWidgetID">
                            <?php
                            foreach ( $widgets as $item ): ?>
                                <option <?php echo (get_option('aklamatorPopUpWidgetID') == $item->uniq_name)? 'selected="selected"' : '' ;?> value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>



                    <p>
                        <input type="checkbox" id="aklamatorPopUpActive" name="aklamatorPopUpActive" <?php echo ((get_option("aklamatorPopUpActive") == 'on')? 'checked="checked"' : ''); ?> >
                        <strong>Enable</strong> PopUp widget
                    </p>
                    <br>
                    <h1>Show Classic widget</h1>
                    <h4>Select widget to be shown on bottom of the each:</h4>

                    <label for="aklamatorPopSingleWidgetTitle">Title Above widget (Optional): </label>
                    <input type="text" style="width: 300px; margin-bottom:10px" name="aklamatorPopSingleWidgetTitle" id="aklamatorPopSingleWidgetTitle" value="<?php echo (get_option("aklamatorPopSingleWidgetTitle")); ?>" maxlength="999" />


                    <label for="aklamatorPopSingleWidgetID">Single post: </label>
                    <select id="aklamatorPopSingleWidgetID" name="aklamatorPopSingleWidgetID">
                        <?php
                        foreach ( $classic as $item ): ?>
                            <option <?php echo (get_option('aklamatorPopSingleWidgetID') == $item->uniq_name)? 'selected="selected"' : '' ;?> value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input style="margin-left: 5px;" id="preview_single" type="button" class="button primary big submit" onclick="myFunction(jQuery('#aklamatorPopSingleWidgetID option[selected]').val())" value="Preview" <?php echo get_option('aklamatorPopSingleWidgetID')=="none"? "disabled" :"" ;?>>
                    </p>

                    <p>
                        <label for="aklamatorPopPageWidgetID">Single page: </label>
                        <select id="aklamatorPopPageWidgetID" name="aklamatorPopPageWidgetID">
                            <?php
                            foreach ( $classic as $item ): ?>
                                <option <?php echo (get_option('aklamatorPopPageWidgetID') == $item->uniq_name)? 'selected="selected"' : '' ;?> value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input style="margin-left: 5px;" type="button" id="preview_page" class="button primary big submit" onclick="myFunction(jQuery('#aklamatorPopPageWidgetID option[selected]').val())" value="Preview" <?php echo get_option('aklamatorPopPageWidgetID')=="none"? "disabled" :"" ;?>>

                    </p>

                </div>

                <input id="aklamator_pop_save" class="aklamator_INlogin" style ="margin: 0; border: 0; float: left;" type="submit" value="<?php echo (_e("Save Changes")); ?>" />
                <?php if(!isset($this->api_data->flag) || !$this->api_data->flag): ?>
                    <div style="float: left; padding: 7px 0 0 10px; color: red; font-weight: bold; font-size: 16px"> <-- In order to proceed save changes</div>
                <?php endif ?>


            </form>
        </div>

        <div style="clear:both"></div>


        <?php if (isset($this->curlfailovao) && $this->curlfailovao && $this->application_id != ''): ?>
            <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">
                <h2 style="color:red">Error communicating with Aklamator server, please refresh plugin page or try again later. </h2>
            </div>
        <?php endif;?>
        <?php if(!isset($this->api_data->flag) || !$this->api_data->flag): ?>
            <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">
                <a href="<?php echo $this->getSignupUrl(); ?>" target="_blank"><img style="border-radius:5px;border:0px;" src="<?php echo POP_AKLA_PLUGIN_URL.'images/teaser-810x262.png' ;?>" /></a>
            </div>
        <?php else : ?>
            <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">
                <!-- Start of dataTables -->
                <div id="aklamatorPro-options">
                    <h1>Your Popup Widgets</h1>
                    <div>In order to add new widgets or change dimensions please <a href="<?php echo $this->aklamator_url ;?>login" target="_blank">login to aklamator</a></div>
                </div>
                <br>
                <table cellpadding="0" cellspacing="0" border="0"
                       class="responsive dynamicTable display table table-bordered" width="100%">
                    <thead>
                    <tr>

                        <th>Name</th>
                        <th>Domain</th>
                        <th>Settings</th>
                        <th>Image size</th>
                        <th>Column/row</th>
                        <th>Created At</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->api_data->data as $item): ?>

                        <tr class="odd">
                            <td style="vertical-align: middle;" ><?php echo $item->title; ?></td>
                            <td style="vertical-align: middle;" >
                                <?php foreach($item->domain_ids as $domain): ?>
                                    <a href="<?php echo $domain->url; ?>" target="_blank"><?php echo $domain->title; ?></a><br/>
                                <?php endforeach; ?>
                            </td>
                            <td style="vertical-align: middle">
                                <div style="float: left; margin-right: 10px" class="button-group">
                                    <input type="button" class="button primary big submit" onclick="myFunction('<?php echo $item->uniq_name; ?>')" value="Preview Widget">
                                </div>
                            </td>
                            <td style="vertical-align: middle;" ><?php echo "<a href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Click & Login to change'>$item->img_size px</a>";  ?></td>
                            <td style="vertical-align: middle;" ><?php echo "<a href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Click & Login to change'>".$item->column_number ." x ". $item->row_number."</a>"; ?>

                                <div style="float: right;">
                                    <?php echo "<a class=\"btn btn-primary\" href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Edit widget settings'>Edit</a>"; ?>
                                </div>

                            </td>
                            <td style="vertical-align: middle;" ><?php echo $item->date_created; ?></td>


                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Settings</th>
                        <th>Immg size</th>
                        <th>Column/row</th>
                        <th>Created At</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">
                <!-- Start of dataTables -->
                <div id="aklamatorPro-options">
                    <h1>Your Classic Widgets</h1>
                    <div>In order to add new widgets or change dimensions please <a href="<?php echo $this->aklamator_url ;?>login" target="_blank">login to aklamator</a></div>
                </div>
                <br>
                <table cellpadding="0" cellspacing="0" border="0"
                       class="responsive dynamicTable display table table-bordered" width="100%">
                    <thead>
                    <tr>

                        <th>Name</th>
                        <th>Domain</th>
                        <th>Settings</th>
                        <th>Image size</th>
                        <th>Column/row</th>
                        <th>Created At</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->api_data->classic as $item): ?>

                        <tr class="odd">
                            <td style="vertical-align: middle;" ><?php echo $item->title; ?></td>
                            <td style="vertical-align: middle;" >
                                <?php foreach($item->domain_ids as $domain): ?>
                                    <a href="<?php echo $domain->url; ?>" target="_blank"><?php echo $domain->title; ?></a><br/>
                                <?php endforeach; ?>
                            </td>
                            <td style="vertical-align: middle">
                                <div style="float: left; margin-right: 10px" class="button-group">
                                    <input type="button" class="button primary big submit" onclick="myFunction('<?php echo $item->uniq_name; ?>')" value="Preview Widget">
                                </div>
                            </td>
                            <td style="vertical-align: middle;" ><?php echo "<a href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Click & Login to change'>$item->img_size px</a>";  ?></td>
                            <td style="vertical-align: middle;" ><?php echo "<a href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Click & Login to change'>".$item->column_number ." x ". $item->row_number."</a>"; ?>

                                <div style="float: right;">
                                    <?php echo "<a class=\"btn btn-primary\" href = \"$this->aklamator_url"."widget/edit/$item->id\" target='_blank' title='Edit widget settings'>Edit</a>"; ?>
                                </div>

                            </td>
                            <td style="vertical-align: middle;" ><?php echo $item->date_created; ?></td>


                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Settings</th>
                        <th>Immg size</th>
                        <th>Column/row</th>
                        <th>Created At</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="right" style="float: right;">
        <!-- right sidebar -->
        <div class="right_sidebar">

            <iframe width="330" height="1024" src="<?php echo $this->aklamator_url; ?>wp-sidebar/right?plugin=popup" frameborder="0"></iframe>
        </div>
        <!-- End Right sidebar -->
    </div>
</div>





