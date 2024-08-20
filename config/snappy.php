<?php

return array(


    'pdf' => array(
        'enabled' => true,
       'binary'  => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
   //     'binary'  => base_path('vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => base_path('vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ), 


);
