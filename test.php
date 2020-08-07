<?php
	
	session_start();

	unset($_SESSION['access']);
	
	if(isset($_POST['username']) && isset($_POST['password'])){

		$hubGroup = array();
		$hubMemberships = array();
		$adMemberships = array();
		$hubGroups = array();
		$find = "'";
		$replace = "''";
		
		$username = $_POST['username'];
		$password = $_POST['password'];

		$_SESSION['username'] = $username;
		
		include("resources/ldapconfig.php");
		
		//Array of groups that can be considered when checking access level. Comes from AD.
		$hubGroups = array(
			"CN=IT Hub - Global Admins,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Loan Register Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",			
			"CN=IT Hub - Loan Register User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Form Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Form Global Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Ticket Scheduler,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - RAP Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - RAP Approver - TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - RAP Approver - Manager,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - RAP Approver - CCM,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - RAP Payer,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Travel System Support Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Travel System Support User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Travel System Support Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - ININ Recording Archive User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Product Team,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Manila,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - TSS,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Read Only,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Error Module,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Manila TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Product Load Tracker - Dashboard User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - 1300 Number Manager - Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - 1300 Number Manager - Editor,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - 1300 Number Manager - User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Hardware Procurement - Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Hardware Procurement - Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Hardware Procurement - Purchaser,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - FCX Vouchers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Auto-ma-Bill Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - Auto-ma-Bill Approvers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - User Lookup,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local",
			"CN=IT Hub - PowerShell Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"
		);

		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		$bind = @ldap_bind($ldap, $ldaprdn, $password);

		if ($bind) {
			$filter="(sAMAccountName=$username)"; //Search filter
			$result = ldap_search($ldap,"DC=rewardscorp,DC=local",$filter); //Create search query
			ldap_sort($ldap,$result,"sn"); //Sort result
			$info = ldap_get_entries($ldap, $result); //Complete query
			for ($i=0; $i<$info["count"]; $i++){ 
				if($info['count'] > 1)
					break;
				
				//Set Variables
				if(isset($info[$i]['memberof'])){
					$adMemberships = $info[$i]['memberof'];
				}
				
				$j = 0;

				//Remove 'count' from Array
				unset($adMemberships['count']);

				//If any AD Memberships set
				if($adMemberships){
				
					//For each hub group, see if the user is part of it and if so set a SESSION variable to prove their access level.
					foreach($hubGroups as $hubGroup) {
						if(in_array($hubGroup, $adMemberships)){
							
							array_push($hubMemberships, $hubGroup);

							if ($hubGroup == "CN=IT Hub - Global Admins,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								//Set Global Admin Session Variable
								$_SESSION['access'][$j] = "0WBGH6EgMSiNoW";
							}
							
							if ($hubGroup == "CN=IT Hub - Ticket Scheduler,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								//Set Ticket Scheduler Session Variable
								$_SESSION['access'][$j] = "IIjbXpulA1GTfk";
							}
							
							if ($hubGroup == "CN=IT Hub - Loan Register Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								//Set Loan Register Session Variable
								$_SESSION['access'][$j] = "xtFhVL61P4S1KQ";
							}
							
							if ($hubGroup == "CN=IT Hub - Form Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "jaV9f9Ag0rMPfe";
							}
							
							if ($hubGroup == "CN=IT Hub - RAP Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "r66g8Cg7U7QAmG";
							}
							
							if ($hubGroup == "CN=IT Hub - RAP Approver - TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "XX8AoRliUX06b7";
							}

							if ($hubGroup == "CN=IT Hub - RAP Approver - Manager,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "HH3q4oSJ4Z09";
							}

							if ($hubGroup == "CN=IT Hub - RAP Approver - CCM,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "8qTeBjGy6VWs";
							}

							if ($hubGroup == "CN=IT Hub - RAP Payer,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "y4R3142s8ifcEI";
							}

							if ($hubGroup == "CN=IT Hub - Travel System Support Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "WW657eU2F9UUlC";
							}	

							if ($hubGroup == "CN=IT Hub - Travel System Support User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "nq8iheETfEk1b8";
							}

							if ($hubGroup == "CN=IT Hub - Travel System Support Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "N4yzFUeijk5UY0";
							}

							if ($hubGroup == "CN=IT Hub - ININ Recording Archive User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "247qsqZETGnFKq";
							}

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Product Team,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "QvPEmL7MV23R2Y";
							}	

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Manila,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "9J7SrQUfDfBuNu";
							}	

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - TSS,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "eVoOiKgE6E17Wl";
							}

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Read Only,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "88CxBs7r4V9x2l";
							}
							if ($hubGroup == "CN=IT Hub - Form Global Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "PIBNv1AP4d8d5q";
							}
							if ($hubGroup == "CN=IT Hub - Loan Register User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "Q5IzH2Z43j3Fb6";
							}

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Error Module,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "o8MDY3j4YYjVOl";
							}

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Manila TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "ZcAa47lMIh4X11";
							}

							if ($hubGroup == "CN=IT Hub - Product Load Tracker - Dashboard User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "di19giVOcI4VL1";
							}

							if ($hubGroup == "CN=IT Hub - 1300 Number Manager - Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "98SL1sJLpjfziy";
							}

							if ($hubGroup == "CN=IT Hub - 1300 Number Manager - Editor,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "3i4wI3XM8fgNmx";
							}

							if ($hubGroup == "CN=IT Hub - 1300 Number Manager - User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "XQlW4z6!X0fjIB";
							}

							if ($hubGroup == "CN=IT Hub - Hardware Procurement - Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "4QmzQ9jwg2nXlH";
							}

							if ($hubGroup == "CN=IT Hub - Hardware Procurement - Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "XP6TbSXW6y3jvS";
							}

							if ($hubGroup == "CN=IT Hub - Hardware Procurement - Purchaser,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "6vF8J1D2Lv3mmA";
							}

							if ($hubGroup == "CN=IT Hub - FCX Vouchers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "75gTuzbaw9M0kz";
							}

							if ($hubGroup == "CN=IT Hub - Auto-ma-Bill Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "idNC5MO3UNdTua";
							}

							if ($hubGroup == "CN=IT Hub - Auto-ma-Bill Approvers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "e9AS3IbmzduS6g";
							}

							if ($hubGroup == "CN=IT Hub - User Lookup,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "qEiGx4dBO6fAYm";
							}

							if ($hubGroup == "CN=IT Hub - PowerShell Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
								
								$_SESSION['access'][$j] = "8HnX@mh5S0LEq@";
							}
							
						}
						
						$j++;
					}
				}


				//For each of the user's groups, see if it is part of a hub group (nested) and if so set a SESSION variable to prove their access level.
				foreach($adMemberships as $adMembership){

					$adMembership = explode(",", $adMembership); //Split the name into parts
					$adMembership = $adMembership[0];  //Get the first part of the group name

					$findstring = array('(',')'); //Find criteria
					$escapestring = array('\(','\)'); //Replace criteria
					$adMembership = str_replace($findstring,$escapestring,$adMembership); //Replace brackets with /( /) to stop them from messing up the query
				
					$filter="(&(objectCategory=group)(".$adMembership."))"; //Search for groups only
					$attr = array("memberof"); //Only return memberof attribute
					$result = ldap_search($ldap,"DC=rewardscorp,DC=local",$filter, $attr); //Build query
					ldap_sort($ldap,$result,"sn"); //Sort results
					$info2 = ldap_get_entries($ldap, $result); //Complete query
					for ($i=0; $i<$info["count"]; $i++){
						if($info['count'] > 1)
							break;

						//If result is a member of any groups
						if(isset($info2[0]['memberof'])){
							$memberof = $info2[0]['memberof'];
							//Cycle through them
							foreach($memberof as $adGroupMembership) {
								//If the group is a member of a hub group, set a session variable for the required access level
								if(in_array($adGroupMembership, $hubGroups)){

									if ($adGroupMembership == "CN=IT Hub - Global Admins,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										//Set Global Admin Session Variable
										$_SESSION['access'][$j] = "0WBGH6EgMSiNoW";
									}
									
									if ($adGroupMembership == "CN=IT Hub - Ticket Scheduler,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										//Set Ticket Scheduler Session Variable
										$_SESSION['access'][$j] = "IIjbXpulA1GTfk";
									}
									
									if ($adGroupMembership == "CN=IT Hub - Loan Register Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										//Set Loan Register Session Variable
										$_SESSION['access'][$j] = "xtFhVL61P4S1KQ";
									}
									
									if ($adGroupMembership == "CN=IT Hub - Form Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "jaV9f9Ag0rMPfe";
									}
									
									if ($adGroupMembership == "CN=IT Hub - RAP Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "r66g8Cg7U7QAmG";
									}
									
									if ($adGroupMembership == "CN=IT Hub - RAP Approver - TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "XX8AoRliUX06b7";
									}

									if ($adGroupMembership == "CN=IT Hub - RAP Approver - Manager,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "HH3q4oSJ4Z09";
									}

									if ($adGroupMembership == "CN=IT Hub - RAP Approver - CCM,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "8qTeBjGy6VWs";
									}

									if ($adGroupMembership == "CN=IT Hub - RAP Payer,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "y4R3142s8ifcEI";
									}

									if ($adGroupMembership == "CN=IT Hub - Travel System Support Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "WW657eU2F9UUlC";
									}

									if ($adGroupMembership == "CN=IT Hub - Travel System Support User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
										
										$_SESSION['access'][$j] = "nq8iheETfEk1b8";
									}

									if ($adGroupMembership == "CN=IT Hub - Travel System Support Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "N4yzFUeijk5UY0";
									}

									if ($adGroupMembership == "CN=IT Hub - ININ Recording Archive User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "247qsqZETGnFKq";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Product Team,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "QvPEmL7MV23R2Y";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Manila,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "9J7SrQUfDfBuNu";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - TSS,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "eVoOiKgE6E17Wl";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Read Only,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "88CxBs7r4V9x2l";
									}

									if ($adGroupMembership == "CN=IT Hub - Form Global Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "PIBNv1AP4d8d5q";
									}

									if ($adGroupMembership == "CN=IT Hub - Loan Register User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "Q5IzH2Z43j3Fb6";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Error Module,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "o8MDY3j4YYjVOl";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Manila TL,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "ZcAa47lMIh4X11";
									}

									if ($adGroupMembership == "CN=IT Hub - Product Load Tracker - Dashboard User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "di19giVOcI4VL1";
									}

									if ($adGroupMembership == "CN=IT Hub - 1300 Number Manager - Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "98SL1sJLpjfziy";
									}

									if ($adGroupMembership == "CN=IT Hub - 1300 Number Manager - Editor,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "3i4wI3XM8fgNmx";
									}

									if ($adGroupMembership == "CN=IT Hub - 1300 Number Manager - User,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "XQlW4z6!X0fjIB";
									}

									if ($adGroupMembership == "CN=IT Hub - Hardware Procurement - Requester,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "4QmzQ9jwg2nXlH";
									}

									if ($adGroupMembership == "CN=IT Hub - Hardware Procurement - Approver,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "XP6TbSXW6y3jvS";
									}

									if ($adGroupMembership == "CN=IT Hub - Hardware Procurement - Purchaser,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "6vF8J1D2Lv3mmA";
									}

									if ($adGroupMembership == "CN=IT Hub - FCX Vouchers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "75gTuzbaw9M0kz";
									}

									if ($adGroupMembership == "CN=IT Hub - Auto-ma-Bill Admin,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "idNC5MO3UNdTua";
									}

									if ($adGroupMembership == "CN=IT Hub - Auto-ma-Bill Approvers,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "e9AS3IbmzduS6g";
									}

									if ($adGroupMembership == "CN=IT Hub - User Lookup,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "qEiGx4dBO6fAYm";
									}

									if ($adGroupMembership == "CN=IT Hub - PowerShell Users,OU=IT Hub,OU=ITG Groups,DC=rewardscorp,DC=local"){
							
										$_SESSION['access'][$j] = "8HnX@mh5S0LEq@";
									}
								}
						
								$j++;
							}
						}
					}
				}
				
				//Allow access to Home module as long as user successfully authenticated with AD
				$_SESSION['access'][$j] = "X7OlUw3oppqhzO";
				//Set Session Variables
				$_SESSION['name'] = $info[0]["cn"][0];
				$_SESSION['email'] = strtolower($info[0]["mail"][0]);
			}
			
			include("get-profile-photo.php");

			@ldap_close($ldap);


			//If there is a URL detected in the SESSION (comes from an email link, currently being used in RAP Module)
			if(isset($_SESSION['url'])) {
				if($_SESSION['url'] !== ""){
					//Set URL Variable
					$url = $_SESSION['url'];
					//Redirect page to the URL after successful authentication
					header("location: $url");
				}
			}

			//If no URL detected, rediret to default index page
			else {
				header("location: modules/home/index.php");
				
			}
		} 
		
		//If authorisation fails, send back to login page with error code to trigger message
		else {
			header("location: index.php?error=1");
		}
	}

	?> 
