<?php
// File: templates/dashboard/wrapper.php

defined('ABSPATH') || exit;
?>

<section id="hdlDashboard">
    <div class="container">
        <div class="tabs" style="padding-top: 1px;">
            <input type="radio" id="tab0" name="tab-control" checked />
            <input type="radio" id="tab1" name="tab-control" />
            <input type="radio" id="tab2" name="tab-control" />
            <input type="radio" id="tab3" name="tab-control" />
            <input type="radio" id="tab4" name="tab-control" />

            <style>
                #hdlDashboard ul{
	background: white;
  padding: 15px 0 !important;
  border-radius: 50px;
  margin-bottom: 0 !important;
  margin-right: auto !important;
  margin-left: auto !important
}
            </style>
            <ul class="container">
                <li><label for="tab0"><span class="pe-2"></span><span>Home</span></label></li>
                <li><label for="tab1"><span class="pe-2"></span><span>Order History</span></label></li>
        
                <li><label for="tab2"><span class="pe-2"></span><span>Message Center</span></label></li>
                <li><label for="tab3"><span class="pe-2"></span><span>Patient Profile</span></label></li>
                <li><label for="tab4"><span class="pe-2"></span><span>Visits</span></label></li>
              
            </ul>

            <div class="slider">
                <div class="indicator"></div>
            </div>

            <div class="content">
                <section>
                    <h2>Home</h2>
                    <?php hdl_get_template('dashboard/home'); ?>
                      <?php hdl_get_template('dashboard/lab-orders'); ?>
                </section>

                <section>
                    <h2>Order History</h2>
                    <div class="inner-content">
                        <?php hdl_get_template('dashboard/show-orders'); ?>
                    </div>
                </section>
                <section>
                    <h2>Lab Orders</h2>
                           <iframe 
  src="https://healsend.com/chat-app/" 
  width="100%" 
  height="1000" 
  style="border: none;" 
  loading="lazy"
></iframe>

                  
                </section>
                <section>
                    <h2>Patient Profile</h2>
                    <?php hdl_get_template('dashboard/patient-profile', ['user' => $user]); ?>
                </section>
                <section>
                    <h2>Visits</h2>
                    <?php hdl_get_template('dashboard/returns'); ?>
                    <iframe 
  src="https://healsend.com/visit/" 
  width="100%" 
  height="1000" 
  style="border: none;" 
  loading="lazy"
></iframe>

                </section>
                
            </div>
        </div>
    </div>
</section>