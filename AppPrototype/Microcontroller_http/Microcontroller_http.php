<?php
//retrieve data from microcontroller check with mysql 

    require '1_connectDB.php';

    //device key value
    $device_key_value = "123";


    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $device_key = test_input($_POST['device_key']);

        if($device_key_value == $device_key) {
            $QR_id = test_input($_POST['QR_id']);
            $start_date = test_input($_POST['start_date']);
            $end_date = test_input($_POST['end_date']);

            $sql = "select * from QR_validity where QR_id = '".$QR_id."' and start_date = '".$start_date."' and end_date = '".$end_date."' ";
            $res = $conn->query($sql);

            if($res->num_rows != 0) {
                $row = $res->fetch_assoc();
            
                //time variable
                //sever time
                $current_time = date_create(date("Y-m-d H:i:s"));
                //QR end time
                $QRend_time = date_create($row['end_date']);

                //remaining time
                $remain_time = date_diff($QRend_time,$current_time);
                $remain_day = (int)$remain_time->format("%R%a");
                $remain_hour = (int)$remain_time->format("%R%h");
                $remain_min = (int)$remain_time->format("%R%i");
                $remain_sec = (int)$remain_time->format("%R%s");

                if($remain_day >= 0 && $remain_hour >= 0 && $remain_min >= 0 && $remain_sec >= 0) {
                    //if qr counter is 0 allow entry(1) and update qr to 1 for single use only.
                    // if qr counter is 1 do not allow entry(0)
                    if($row['counter'] == 0) {
                        $counter = 1;
                        $sql_update = "update  QR_validity set counter = '".$counter."' where QR_id = '".$QR_id."' and start_date = '".$start_date."' and end_date = '".$end_date."' ";
                        $conn->query($sql_update);
                        //entry allowed
                        echo 1;
                    }
                    elseif ($row['counter'] == 1) {
                        //entry denied
                        echo 0;
                    }
                }
                else
                //QR code expired
                echo 2;
            }
            else
            //QR code does not exist
            echo 3;
        }
        else
        //device id not the same 
        echo 4;
    }
    else
    echo "Not post method";

    $conn->close();

    //ensure recived data is utf-8 encoded
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


?>
