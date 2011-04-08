<?php
$API = array(
    'TestAction'=>array(
        'methods'=>array(
            'doEcho'=>array(
                'len'=>1
            ),
            'multiply'=>array(
                'len'=>1
            ),
            'getTree'=>array(
                    'len'=>1
            )
        )
    ),

    'Profile'=>array(
        'methods'=>array(
            'getBasicInfo'=>array(
                'len'=>2
            ),
            'getPhoneInfo'=>array(
                'len'=>1
            ),
            'getLocationInfo'=>array(
                'len'=>1
            ),
            'updateBasicInfo'=>array(
                'len'=>2,
                'formHandler'=>true
            )
        )
    )
);