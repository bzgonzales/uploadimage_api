<?php

	include_once("config.php");


	if($_POST[]){
		save_phone_data();
	}

	

	function login_verification(){


		$user_name 	= $_POST['user_name'];
		$password 	= $_POST['password'];

		$err = 0;

		if(strlen(trim($user_name)) == 0){
			$err++;
		}

		if(strlen(trim($password)) == 0){
			$err++;
		}



		if($err == 0){

			$verify_login = $this->model->verify_login($user_name,$password);

			if($verify_login){

				$response = (object)$verify_login;

				if($response->status == 1){

					// set session here 
					Session::initialize();
					$insert_ses['user_id']			= $response->user_id;
					$insert_ses['user_type']		= $response->user_type;
					$insert_ses['email_address']	= $response->email_address;
					$insert_ses['user_name']		= $response->user_name;
					Session::set('user',$insert_ses);
					Session::set('loggedin',true);

					$result['status'] = 'success';
					$result['url'] = base_url.'inventory';

				} elseif($response->status == 0){
					$result['status'] = 'inactive';
				} else {
					$result['status'] = 'error';
				}

			} else {
				$result['status'] = 'error';
			}

		} else {
			$result['status'] = 'error';
		}

		//header('Content-Type: application/json');
		print json_encode($result);
	}

	function logout(){

		// delete session
		Session::initialize();
		Session::destroy();
		header("Location: ".base_url."login");
		die();

	}


	function load_items_callback() {

		if(isset($_POST)):
			$items = $this->model->get_items($_POST);
		else:
			$items = 0;
		endif;
		
		if ($items->records_total > 0):
			foreach ($items->records_filtered as $key => $row):
				$qty = (int)$row['item_s_qty'] - (int)$row['item_r_qty'];
				$qty = ($qty) ? $qty : 0;
				$content[] = array(
									'item_code' 			=> $row['item_code'],
									'item_description' 		=> $row['item_description'],
									'qty_stock' 			=> $row['item_s_qty'],
									'qty_reserve' 			=> $row['item_r_qty'],
									'qty_available' 		=> $qty,
								);		

			endforeach;
			$result['recordsTotal']    = $items->records_total;
			$result['recordsFiltered'] = $items->records_total;
		else:
			$content                   = "";
			$result['recordsTotal']    = 0;
			$result['recordsFiltered'] = 0;
		endif;

		$result['data'] = $content;
		header('Content-Type: application/json');
		print json_encode($result);

	}

	function load_orders_callback() {

		Session::initialize();
		$user = Session::get('user');
		$usertype = @$user['user_type'];


		if(isset($_POST)):
			$orders = $this->model->get_orders($_POST);
		else:
			$orders = 0;
		endif;
		
		if ($orders->records_total > 0):
			foreach ($orders->records_filtered as $key => $row):

				// customer restriction code
				if($usertype!='customer'){
					$action = '<a href="#" class="call-order-modal btn btn-default btn-sm" data-toggle="modal" data-target="#order-modal" data-order-id="' . $row['phone_id']. '"><i class="fa fa-edit"></i></a>';
				}else{
					$action = '';
				}

				$items = $this->model->get_items(array('clientids' => $row['client_id']));

				if ($items->records_total > 0):
					$list_items = '<ol>';
					foreach ($items->records_filtered as $key => $row2):	
						
							$list_items .= '<li>'.$row2['item_description'].'</li>';
					

					endforeach;
					$list_items .= '</ol>';

				else:
					$list_items = 'No Items yet';
				endif;



				$content[] = array(
									'client' 		=> $row['client_name'],
									'delivery' 		=> $row['delivery_date'],
									'c_name' 		=> $row['customer_name'],
									'c_address' 	=> $row['customer_address'],
									'zip' 			=> $row['zip_code'],
									'items' 		=> $list_items,
									'action' 		=> $action,
								);		



			endforeach;
			$result['recordsTotal']    = $orders->records_total;
			$result['recordsFiltered'] = $orders->records_total;
		else:
			$content                   = "";
			$result['recordsTotal']    = 0;
			$result['recordsFiltered'] = 0;
		endif;

		$result['data'] = @$content;
		header('Content-Type: application/json');
		print json_encode($result);
		
	}


	function phone_data_callback(){

		session::initialize();
		$user = Session::get('user');
		$usertype = @$user['user_type'];

		$orderid = $_POST['phone_id'];
			
		$order = $this->model->get_order($orderid);
		
		//functions::print_in($order);

		$clients = $this->model->get_clients();

		$html = '
                    <form method="post" id="submit-order-form">
                        <div class="card border-primary rounded-0">

                            <div class="card-body p-3">
                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-user text-info"></i></div>
                                        </div>';
                               $html .='<select id="select-form-clients" class="form-control" data-live-search="true">
                               				<option value="0" selected disabled>Select Clients</option>';
                                        foreach ($clients as $value) {
                                        	 $val = (object)$value;

                                        	 $selected = ($val->client_id==@$order['client_id']) ? 'selected' : '';

                                        	 $html .= '<option '.$selected.' value="'.$val->client_id.'">'.$val->client_name.'</option>';
                                        }
                               $html .='</select>';

        $html .=                    '</div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-calendar text-info"></i></div>
                                        </div>
                                        <input type="text" class="form-control" id="deliverydate" name="deliverydate" value="'.@$order['delivery_date'].'" placeholder="Delivery Date" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-users text-info"></i></div>
                                        </div>
                                        <input type="text" class="form-control " value="'.@$order['customer_name'].'" id="cust_name" name="cust_name" placeholder="Customer Name" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-map-marker-alt text-info"></i></div>
                                        </div>
                                        <input type="text" class="form-control " value="'.@$order['customer_address'].'" id="cust_address" name="cust_address" placeholder="Customer Address" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-file-archive text-info"></i></div>
                                        </div>
                                        <input type="number" class="form-control " value="'.@$order['zip_code'].'" id="zip_code" name="zip_code" placeholder="Zip/Postal Code" required>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </form>

		';

		$buttons = '<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>';

		if($orderid=='add'){
			$buttons .= '<button type="button" class="btn btn-primary save-changes-btn" data-status="1" data-order-id="'.@$orderid.'">Submit</button>';
		}else{

			$buttons .= '<button type="button" class="btn btn-primary save-changes-btn" data-status="1" data-order-id="'.@$orderid.'">Update</button>';

			$buttons .= '<button type="button" class="btn btn-warning save-changes-btn" data-status="3" data-order-id="'.@$orderid.'">Cancel</button>';
			if($usertype=='admin'){
				$buttons .= '<button type="button" class="btn btn-danger save-changes-btn" data-status="0" data-order-id="'.@$orderid.'">Delete</button>';
			}
		}


		$result['form'] = @$html;
		$result['buttons'] = @$buttons;
		header('Content-Type: application/json');
		print json_encode($result);


	}

	function save_phone_data(){

		if($_POST['status']=='add'){
			$update = mysqli_query($mysqli, "INSERT INTO phonebook_details(full_name,address,phone,gender) VALUES('$_POST["full_name"]','$_POST["address"]','$_POST["phone"]','$_POST["gender"]')");
		}elseif($_POST['status']=='edit'){
			$update = mysqli_query($mysqli, "UPDATE phonebook_details SET full_name=$_POST["full_name"],address=$_POST["address"],phone='$_POST["phone"]' WHERE id=$_POST["phoneid"]");
		}else{
			$update = mysqli_query($mysqli, "DELETE FROM phonebook_details WHERE id=$_POST["phoneid"]");

		}

		if($updated){
			switch ($_POST['status']) {
			    case 'add':
			        $label = 'saved';
			        break;
			    case 'edit':
			        $label = 'saved';
			        break;
			    case 'delete':
			        $label = 'deleted';
			        break;
			}
		}else{
			$label = 'error';
		}

		$result['success'] = $label;
		header('Content-Type: application/json');
		print json_encode($result);


	}


