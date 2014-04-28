<?php
class MW_Mcore_Helper_Data extends Mage_Core_Helper_Abstract
{
   // $module_special_name is module when click extend trial module
    public  $all_active = 1;
    const MESS_97 = " extension is activated successfully!";
    const MESS_100 = "Activate failed. Please enter a valid activation key."; 
    
	public function updatestatus($module_special_name="",$timeend=0) //timeend!=0 when checkstatus extendtrial
	{	
		$modules_install = $this->getModuleCompany();
    	if(count($modules_install)>0)
    	{
    		 foreach ($modules_install as $module_install)
    		 {  		 	
    		 	
    		 	 if($this->checkExistModule($module_install)) // this module is managed
    		 	 {
					if((Mage::getStoreConfig($this->encryptModuleName($module_install)))== "") //value null may be because use dele value, extend trial 7 day, 
					{		
							$this->all_active = 0;		
							$type_comment = $this->getModuleTypeComment($module_install);
    		 	 			$this->startTrial($module_install,$type_comment);						
					}
					else 
					{		
							$this->checkStatus($module_install,$module_special_name,$timeend);
					}
    		 	 }
    		 	 else // start trial when install mcore
    		 	 {
    		 	 			$this->all_active = 0;    
    		 	 			$type_comment = $this->getModuleTypeComment($module_install);		 	 			
    		 	 			$this->startTrial($module_install,$type_comment);
    		 	 }
    		 	 $this->reAssignExtensionInfo($module_install); 
			 }	
			 Mage::getModel('core/config')->saveConfig('mcore/allextension/activated',$this->all_active);
			 Mage::getConfig()->reinit();						
    	} 
		if (!class_exists('SoapClient') && $this->all_active == 0)
    		{
    		 	Mage::getModel('core/config')->saveConfig('mcore/errorSoap',1);
				 Mage::getConfig()->reinit();
			}
			else 
			{
				Mage::getModel('core/config')->saveConfig('mcore/errorSoap',0);
				 Mage::getConfig()->reinit();
			}	
	}
		
   function startTrial($module_name,$type)
	{
		$this->insertLicenseTrial(strtolower($module_name),$type);
		$starttime = strtotime(date('Y-m-d H:i:s'));			
		$status=1; 
		$endtime = $starttime + $this->timeTrial();
		$module_infs = $starttime.','.$status.','.$endtime;	
		$module_infs_value = Mage::helper('core')->encrypt($module_infs);
		Mage::getModel('core/config')->saveConfig($this->encryptModuleName($module_name),$module_infs_value);	
		$moduleconfig = $this->getModuleConfig($module_name);
		if(trim($moduleconfig)!="")
			Mage::getModel('core/config')->saveConfig($moduleconfig,1);						
		 Mage::getConfig()->reinit();												  	
	}	
	
	function checkStatus($module_name,$module_special_name,$timeend=0)
	{																	
						$module_infs_value = $this->getConfigValue($this->encryptModuleName($module_name));						
						$module_inf = Mage::helper('core')->decrypt($module_infs_value);						
						$strmcore_module = $module_inf; // string of module info
						$arr_mod_inf = explode(',',$module_inf); // array info
						$timenow = strtotime(date('Y-m-d H:i:s'));
						if(!is_array($arr_mod_inf) || !$this->checkModInfo($arr_mod_inf))
						{	
									$this->all_active = 0;
									$arr_mod_inf[0] = $timenow;		
									$arr_mod_inf[1] = 0;	
									$arr_mod_inf[2] = $timenow +1;		
									$strmcore_module = implode($arr_mod_inf,',');	
									$module_infs_value = Mage::helper('core')->encrypt($strmcore_module);							
									Mage::getModel('core/config')->saveConfig($this->encryptModuleName($module_name),$module_infs_value); // finish trial module change to disable
									//reinit	
									Mage::getConfig()->reinit();									
									$this->disableConfig($module_name);
						}
						else 
						{							
							if(intval($arr_mod_inf[1])!=2) 
							{
								
								$this->all_active = 0;
								if($module_special_name!="" && strtolower($module_special_name) == strtolower($module_name)) // extend trial module from Indexcontroller
								{
									$this->enableConfig($module_name);
																	
								}
								else if(intval($arr_mod_inf[1])==0 || intval($arr_mod_inf[1])==4) 
								{									
									$this->disableConfig($module_name);							
								}								
								else if($timenow>=intval($arr_mod_inf[2]) && (intval($arr_mod_inf[1])==1 || intval($arr_mod_inf[1]) ==3))
								{	
										
									if(intval($arr_mod_inf[1])==3)				
									$arr_mod_inf[1] = 4;
									else 
									$arr_mod_inf[1] = 0;										
									$strmcore_module = implode($arr_mod_inf,',');	
									$module_infs_value = Mage::helper('core')->encrypt($strmcore_module);							
									Mage::getModel('core/config')->saveConfig($this->encryptModuleName($module_name),$module_infs_value); 
									//reinit
									 Mage::getConfig()->reinit();
									$this->disableConfig($module_name);
								}		
							}							
						}
	}
		
	function encryptModuleName($modulename)
	{
		return 'mw_'.md5('mcore/'.strtolower($modulename));
	}
	
	function enctyptModuleNameold($modulename)
	{
		return 'mw/'.md5('mcore/'.strtolower($modulename));
	}
	
	function reAssignExtensionInfo($modulename)
	{
		$configvalue = Mage::getStoreConfig($this-> enctyptModuleNameold($modulename));	
		if($configvalue)
		{	
			Mage::getModel('core/config')->saveConfig($this->encryptModuleName($modulename),$configvalue);			
			Mage::getConfig()->reinit();
			Mage::getModel('core/config')->deleteConfig($this->enctyptModuleNameold($modulename));				
		}		
	}
	
	function checkExistModule($modulename)
	{
		try {
					$resource = Mage::getSingleton('core/resource');
	    			$readConnection = $resource->getConnection('core_read');
	    			$tableName = $resource->getTableName('core_config_data');
	    			$query = "SELECT count(*) FROM ".$tableName." WHERE path ='".$this->encryptModuleName($modulename)."'"; 
   					$value=$readConnection->fetchOne($query);	    						  	  
	    			if($value>0)
	    			return true;
	    			else 
	    			return false; 
		}
		catch (Exception $e)
		{
			return true;
		}
	}
	
	function getConfigValue($configpath)
	{
		try {
					$resource = Mage::getSingleton('core/resource');
	    			$readConnection = $resource->getConnection('core_read');
	    			$tableName = $resource->getTableName('core_config_data');
	    			$query = "SELECT value FROM ".$tableName." WHERE path ='".$configpath."'"; 
   					$value=$readConnection->fetchOne($query);	
   					if($value)   					  						  	  
	    			return $value;
	    			else return ""; 
		}
		catch (Exception $e)
		{
			return "";
		}
	}
	
	function getModuleConfig($modulename)
	{
		
		$key_config="";
		$modules_company = Mage::getStoreConfig('mcore/extensions'); 
		foreach ($modules_company as $key=>$value)
		   	{
			if(strtolower(trim($modulename))==strtolower(trim($value['key'])))
		  			{
		  				if(isset($value['config']) && trim($value['config'])!="")
		    				$key_config = $value['config'];	
		    			
		    			return $key_config;   
		   			}  		
		   	}
		return trim($key_config);
	}
	
	function getModuleRealKey($modulename)
	{		
		$realkey="";
		$modules_company = Mage::getStoreConfig('mcore/extensions'); 
		foreach ($modules_company as $key=>$value)
		   	{
			if(strtolower(trim($modulename))==strtolower(trim($value['key'])))
		  			{
		    			$realkey = $value['key'];	
		    			return $realkey;   
		   			}  		
		   	}
		return $realkey;
	}
	
	function getModuleUrl($modulename)
	{
		$url="";
		$edition = $this->getModuleEdition($modulename);
		$modulename .= $edition;
		
		$modules_company = Mage::getStoreConfig('mcore/extensions'); 
		foreach ($modules_company as $key=>$value)
		   	{
			if(strtolower(trim($modulename))==strtolower(trim($value['key'])))
		  			{
		    			$url = $value['url'];	
		    			return $url; 
		   			}  		
		   	}
		return $url;
	}
	
	function getModuleName($modulename)
	{
		$name="";
		$editionName = $this->getModuleEdition($modulename);
		$modulename .= $editionName;
		$modules_company = Mage::getStoreConfig('mcore/extensions'); 
		
		foreach ($modules_company as $key=>$value)
		   	{		   		
				if(strtolower(trim($modulename))==strtolower(trim($value['key'])))
		  			{
		    			$name = $value['name'];	    	
		    			return $name;
		   			}
		   	}
	}
	
	function getModuleVersion($modulename)
	{	
		return Mage::getConfig()->getNode()->modules->$modulename->version;
	}
	
	function showTimeTrial($modulename,$endtime) // trial or trial again is same
	{
		
		$timenow = strtotime(date('Y-m-d H:i:s'));
		$timeend = $endtime;
		$announce ="";
		$timeexpried = intval($timeend)-intval($timenow);	
		if(intval($timeexpried)>0)
		{												 
			$announce =  ' The trial will expire in <b>'.ceil($timeexpried/(3600*24)).'</b> days.';						
		}
		return '<span class="mw_announce">'.$this->__($announce).'</span>';
	}
	
	//$position = 1, show infomation in mcore configuration, $position = 0 => show infomation on top
	public function showTimeCheckStatus($modulename,$position="")
	{
		$announce ="";
		$module_infs_value = $this->getConfigValue($this->encryptModuleName($modulename));
		$module_inf = Mage::helper('core')->decrypt($module_infs_value);		
		$arr_mod_inf = explode(',',$module_inf);
		$linkactive = "active('".strtolower($modulename)."',1,'live_site')";
		$linkactivedev = "active('".strtolower($modulename)."',1,'dev_site')";
		$encmodule = $this->encryptMyName($modulename);										
		$url = Mage::getUrl('mcore/index/trial/module/'.$encmodule);
		if(isset($arr_mod_inf[1]))
		{
		if(  intval($arr_mod_inf[1])!=2)
		{
			$timenow = strtotime(date('Y-m-d H:i:s'));
			$timeend = $arr_mod_inf[2];
			$timeexpried = intval($timeend)-intval($timenow);	
			
			$day ="days";
			$hour="hours";
			$minute ="minutes";
			$second="seconds";
			if(intval($arr_mod_inf[1])==1)
			{
				if(intval($timeexpried)>0)
				{
						
						if($timeexpried <60) // seconds
						{	
							$announce = 'will expire in <b>1</b> minute. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a> ';			
							if($position=="detail")
							{
								$announce = 'The trial will expire in <b>1</b> minute. ';
							}							
						}
						elseif(ceil($timeexpried/60) <=60)		
						{
							if(ceil($timeexpried/60)==1)
							$minute = "minute";
							$announce = 'will expire in <b>'.ceil($timeexpried/60).' '.$minute.'</b>. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a>';
							if($position=="detail")
							{
							$announce = 'The trial will expire in <b>'.ceil($timeexpried/60).'</b> '.$minute.'. ';
							}
							
						}
						elseif(ceil($timeexpried/(60*60)) <=24) 
						{	
							  if(ceil($timeexpried/3600) == 1)
							  $hour = "hour";						
							  $announce = 'will expire in <b>'.ceil($timeexpried/3600).'</b> hours. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a>';
							  if($position=="detail")
							  {
							  $announce = 'The trial will expire in <b>'.ceil($timeexpried/3600).'</b> hours.';
							  }
						}							
						else 
						{	
							if(ceil($timeexpried/(3600*24)) == 1)
							$day = "day";						 												 
							$announce =  'will expire in <b>'.ceil($timeexpried/(3600*24)).'</b> '.$day.'. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a>';
							if($position=="detail")
							{
							$announce = 'The trial will expire in <b>'.ceil($timeexpried/(3600*24)).'</b> '.$day.'.';
							}
						}
				}
				else 
				{					
					$arr_mod_inf[1]=0;
					$strmcore_module = implode($arr_mod_inf,',');	
				    $module_infs_value = Mage::helper('core')->encrypt($strmcore_module);							
				    Mage::getModel('core/config')->saveConfig($this->encryptModuleName($modulename),$module_infs_value); 	
				    //reinit	   
					Mage::getConfig()->reinit();	
					
					$announce = ' is disabled. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a><span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a>   <span style="padding: 0 5px;">|</span> <a href="'.$url.'">Extend 7 days trial</a>';	
					if($position=="detail")
					{
					 $announce = 'The trial  is expired.';
					}	
				}				
			} 
			else if(intval($arr_mod_inf[1])==3) 
			{	
				if(intval($timeexpried)>0)
				{
				if($timeexpried <60) // seconds
						{	
							$announce = 'will expire in <b>1</b> minute.  <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a>';			
							if($position=="detail")
							{
								$announce = 'The extension will expire in <b>1</b> minute. ';
							}							
						}
						elseif(ceil($timeexpried/60) <=60)		// mintes
						{
							if(ceil($timeexpried/60)==1)
							$minute = "minute";
							$announce = 'will expire in <b>'.ceil($timeexpried/60).' '.$minute.'</b>. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a>';
							if($position=="detail")
							{
							$announce = 'The extension will expire in <b>'.ceil($timeexpried/60).'</b> '.$minute.'. ';
							}
							
						}
						elseif(ceil($timeexpried/(60*60)) <=24) // hours
						{	
							  if(ceil($timeexpried/3600) == 1)
							  $hour = "hour";						
							  $announce = 'will expire in <b>'.ceil($timeexpried/3600).'</b> hours. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a>';
							  if($position=="detail")
							  {
							  $announce = 'The extension will expire in <b>'.ceil($timeexpried/3600).'</b> hours.';
							  }
						}							
						else 
						{	
							if(ceil($timeexpried/(3600*24)) == 1)
							$day = "day";						 												 
							$announce =  'will expire in <b>'.ceil($timeexpried/(3600*24)).'</b> '.$day.'. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a>';
							if($position=="detail")
							{
							$announce = 'The extension will expire in <b>'.ceil($timeexpried/(3600*24)).'</b> '.$day.'.';
							}
						}
				}
				else 
				{
				    $arr_mod_inf[1]=4;
					$strmcore_module = implode($arr_mod_inf,',');	
				    $module_infs_value = Mage::helper('core')->encrypt($strmcore_module);							
				    Mage::getModel('core/config')->saveConfig($this->encryptModuleName($modulename),$module_infs_value); 
					Mage::getConfig()->reinit();
				    			
					$announce = ' is disabled. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a> <span style="padding: 0 5px;">|</span> <a href="'.$url.'">Extend 7 days trial</a>';	
					if($position=="detail")
					{
					  $announce = 'The trial  is expired.';
					}
				}
			}
			else 
			{
				if(intval($arr_mod_inf[1])==0)
				{									
					$announce = ' is disabled. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a> <span style="padding: 0 5px;">|</span> <a href="'.$url.'">Extend 7 days trial</a>';	
					if($position=="detail")
					{
					$announce = 'The trial  is expired.';
					}
				}
				else 
				{				  					
					$announce = ' is disabled. <a  href="javascript:void(0)" onclick="'.$linkactive.'">Activate for Live site</a> <span style="padding: 0 5px;">|</span> <a  href="javascript:void(0)" onclick="'.$linkactivedev.'">Activate for Development</a> <span style="padding: 0 5px;">|</span> <a target="_blank" href="'.$this->getModuleUrl($modulename).'">Buy this module</a> <span style="padding: 0 5px;">|</span> <a href="'.$url.'">Extend 7 days trial</a>';	
					if($position=="detail")
					{
					 $announce = 'The extension is expired.';
					}
				}
			}
		 }	
		}
		else 
		{
			$announce = "You need to logout and then log back in to start this free trial";
		}
			
		return '<span class="mw_announce">'.$this->__($announce).'</span>';
	}
	
	function myConfig()
	{
		return 'mcore/config/enabled';
	}
	function myName()
	{
		return 'MW_Mcore'; 
	}
	
	function getCommentExtendTrial($modulename,$endtime)
	{
		$timeexpried = $this->showTimeTrial($modulename, $endtime);
		return '<font style="color:black; font-weight:bold">Trial</font><p class="note"><span>
			                   		 			<span class="mw_announce">'.$timeexpried.'</span><br> <a id="mcore_active_'.$this->getModuleUrl($modulename).'" class="mw_active">Activate for Live site</a>
			                   		 			<span style="padding:5px;">|</span> <a id="mcore_dev_'.$this->getModuleUrl($modulename).'" class="mw_dev">Activate for Development</a> 
			                   		 			<span style="padding:5px;">|</span> <a class="mw_buy" href="'.$this->getModuleUrl($modulename).'" target="_blank">Buy This Module</a>
						 					</span></p>';
	}
	
	function showMcoreNotification()
	{
		if(!Mage::getSingleton('core/session')->getNotification())
		{
			$xml_module = array();
		    $module_order=0;			
	    	$notification  = array();	
  			$modules_install = $this->getModuleCompany();
  			 	    	
	    	if(count($modules_install)>0)
	    	{
	    		$i = 0;
	    	 	foreach ($modules_install as $module_install)
	    		 {  
	    		 	 if($this->checkExistModule($module_install)) 
	    		 	 {
						$module_infs_value = $this->getConfigValue($this->encryptModuleName($module_install));
						$module_inf = Mage::helper('core')->decrypt($module_infs_value); 
						$module_inf_arr = explode(',', $module_inf);
						$timenow = strtotime(date('Y-m-d H:i:s'));
						$timeend = $module_inf_arr[2];
						$timeexpried = intval($timeend)-intval($timenow);	
						if((($module_inf_arr[1]==1 || $module_inf_arr[1]==3)  && $timeexpried<=$this->timeExtendTrial()) || $module_inf_arr[1]==0 || $module_inf_arr[1]==4)
						{
							$notification[$i][0] = $this->__(' The <span style="font-weight:bold; color:green;">'.$this->getModuleName($module_install)).'</span> extension '.$this->showTimeCheckStatus($module_install);
							$notification[$i][1] = $module_inf_arr[1];
							$notification[$i][2] = strtolower($module_install);
							$i++;
						}	
	    		 	 }	
	    		 }	
	    	} 
	    	Mage::getSingleton('core/session')->setNotification($notification);
	    }   
		else 
    	$notification = Mage::getSingleton('core/session')->getNotification();
    	return $notification;    	
	}
	
	function getCommentActive($client_data,$result)
	{	
		$modulename = $client_data["module_system"];	
					     
		if($client_data['type_site'] !=Mage::getStoreConfig('mcore/type1'))
		{
			if(!$this->checkSatisfies($client_data,$result))
			{					
				$module_infs_value = Mage::getStoreConfig($this->encryptModuleName($modulename));
				$module_inf = Mage::helper('core')->decrypt($module_infs_value);
				$mod_infs= explode(',', $module_inf);		    
			    $strmod = implode($mod_infs,',');
			    $module_infs_value = Mage::helper('core')->encrypt($strmod);			   
			   	if(!is_array($result))
			    echo self::MESS_100;
			}
			else
			{							
				$mod_infs = array();  
				$mod_infs[0]= strtotime(date('Y-m-d H:i:s'))-360000;
			    $mod_infs[1] = Mage::getStoreConfig('mcore/decrt');
			    $mod_infs[2] = strtotime(date('Y-m-d H:i:s'));
			    $strmod = implode($mod_infs,',');
			    $module_infs_value = Mage::helper('core')->encrypt($strmod);			    
			    Mage::getModel('core/config')->saveConfig($this->encryptModuleName($modulename),$module_infs_value);
				$this->enableConfig($modulename);
				Mage::getConfig()->reinit();		
							
			    if(!is_array($result))			     
			     echo "The ".$this->getModuleName($modulename).self::MESS_97;
			}			
		}
		else 
		{		
		  if(!$this->checkSatisfies($client_data,$result))
			{			
				$module_infs_value = Mage::getStoreConfig($this->encryptModuleName($modulename));
				$module_inf = Mage::helper('core')->decrypt($module_infs_value);
				$mod_infs= explode(',', $module_inf);		    
			    $strmod = implode($mod_infs,',');
			    $module_infs_value = Mage::helper('core')->encrypt($strmod);	
			    if(!is_array($result))
			   	 echo self::MESS_100;		    		    
			}
			else 
			{				
				$mod_infs = array();  
				$mod_infs[0]= strtotime(date('Y-m-d H:i:s'));
			    $mod_infs[1] = Mage::getStoreConfig('mcore/decrtdev');
			    $mod_infs[2] = strtotime(date('Y-m-d H:i:s'))+$this->timeDev();
			    $strmod = implode($mod_infs,',');			   
			    $module_infs_value = Mage::helper('core')->encrypt($strmod);
			    Mage::getModel('core/config')->saveConfig($this->encryptModuleName($modulename),$module_infs_value);		   
				Mage::getConfig()->reinit();			   
				$this->enableConfig($modulename);
			    if(!is_array($result))
			   	  echo "The ".$this->getModuleName($modulename).self::MESS_97;		   
			}				
		}			
	}
	
	function encryptMyName($moduleName)
	{		
		return md5(strtolower($moduleName).'/');
	}
	
	function  getModules()
	{
		$myextension = array();
		$modules_company = Mage::getStoreConfig('mcore/extensions');
		if($modules_company)	
		foreach ($modules_company as $key=>$value)
    	{
    		$myextension[] = $value['key'];
    	}
    	return $myextension;
	}
	
	function insertLicenseTrial($module,$type="")
	{		
		$domain = $this->getDomain(); 	   
    	try {
				if($this->checkDomain($domain))
	    		{	 	
					if($module!="" ) 
					{
					    if (class_exists('SoapClient'))
				    		 {
				    		 	if(Mage::getStoreConfig('mcore/errorSoap')==1)
			    		 		Mage::getModel('core/config')->saveConfig('mcore/errorSoap',0);
								Mage::getConfig()->reinit();
					    		$client = new SoapClient(Mage::getStoreConfig('mcore/activelink'));							 	
					        	$session = $client->login(Mage::getStoreConfig('mcore/userapi'),Mage::getStoreConfig('mcore/codeapi'));
							    $result=$client->call($session,'managelicense.insertTrial',array(array('module' =>$module, 'domain'=>$domain,'type'=>$type)));	
					    	}
				    		 else 
				    		 {	    		 	
			    		 		Mage::getModel('core/config')->saveConfig('mcore/errorSoap',1);
								Mage::getConfig()->reinit();
				    		 }						    		
				    	}
			    	}	
    			}
				catch(Exception $e)
				{}
	}
	
	public function checkDomain($domain)
	{
		$valid = preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $domain);
		if(strpos(strtolower($domain),'localhost') !== false || $valid)
		return false;
		return true;
	}
	
	public function activeOnLocal($domain,$type_site="")
	{
		if($type_site=="dev_site")
			return false;
			
		if(strpos(strtolower($domain),'localhost') !== false)		
			return true;
		return false;
	}
	
	public function activeOnDevelopSite($domain,$type_site="")
	{
		if($type_site=="dev_site")
			return false;
			
		$valid = preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $domain);
		if($valid)		
			return true;
		return false;
	}
		
	function timeExtendTrial()
	{
		return 604800;
	}
	function timeTrial()
	{
		return  2592000;
	}
	function timeReTrial()
	{
		return 432000;
	}
	function timeDev()
	{
		return 5184000;
	}
	
	function getDomain()
	{
		$domain = Mage::getBaseUrl();
		
		$str = strtolower($domain);		
		$i=  strpos($str,"index");
				
    	if($i!==false)
		{
			$str= substr($str,0,$i);
		}	
	
		$i2=  strpos($str,"admin");
		if($i2!==false)
		{
			$str= substr($str,0,$i2);
		}
		
		$arr = explode('/', $str);			
		$strreturn = "";
		
		if(isset($arr[0]) && !empty($arr[0]))
			if(($arr[0] == "http:" || $arr[0] == "https:"))
				$strreturn = $arr[0].'//';
			else 
				$strreturn = "http://".$arr[0].'/';	
		
		if(isset($arr[1]) && !empty($arr[1]))
			$strreturn .= $arr[1].'/';
			
		if(isset($arr[2]) && !empty($arr[2]))
			$strreturn .= $arr[2].'/';
			
				
		return $strreturn;
			
	}
	
	function checkModInfo($arrinfo)
	{
		if(count($arrinfo) != 3)
		return false;
		else 
		{
			foreach ($arrinfo as $elm)
			{
				if(!$this->checkIsNumeric($elm))
				return false;
			}
		}
		return true;
	}
	
	function checkIsNumeric($gt)
	{
		if(is_numeric($gt) && $gt>=0)
		{
			return true;
		}
		return false;
	}
	

	function changeRemind()
	{
		$modules_install = $this->getModuleCompany();
		if(count($modules_install)>0)
		{
			foreach ($modules_install as $module)
			if(Mage::getStoreConfig('mw/remind/'.strtolower($module)) == 1)
			{
				Mage::getSingleton('core/config')->saveConfig('mw/remind/'.strtolower($module),0);				
				Mage::getConfig()->reinit();
			}
		}
	}
	
	public function getModuleCompany()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());   
		$modules2 = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules2; 		
		$modules_company = Mage::getStoreConfig('mcore/extensions');	 	
    	$modules_install = array(); 		
    	foreach ($modules_company as $key=>$value)
    	{
    		foreach($modules as $module)
    		{
    			if(strtolower(trim($module))==strtolower(trim($value['key'])) && $modulesArray[$module]->is('active') && !$this->checkIncludedModule($module))
    			{   				
	    			$modules_install[] = $module;	    			
	    			break;
    			}
    		}
    	} 
    	return $modules_install;
	}
	
	//update for free, trial version
	function getModuleType($modulename)
	{	
		return (string) Mage::getConfig()->getNode()->modules->$modulename->type;
	}
	
	//Update get ExtendName
	function getModuleEdition($modulename)
	{		
		$modulename = $this->getModuleRealKey($modulename);		
		return (string) Mage::getConfig()->getNode()->modules->$modulename->edition;
	}
	
	function checkIncludedModule($modulename)
	{
		$include = Mage::getConfig()->getNode()->modules->$modulename->included;			
		if(trim($include)!="")
			return true;
		return false;
	}
	
	function getModuleComment($modulename)
	{		
		$modulename = $this->getModuleRealKey($modulename);		
		return (string) Mage::getConfig()->getNode()->modules->$modulename->comment;
	}
	
	public function checkSatisfies($client_data,$result)
	{		
		$optimisedomain = $this->optimiseDomain($client_data["domain"]);
		$str= $client_data["module"].$client_data["type_site"].$optimisedomain;	
								
		if(is_array($result))
		{			
			if($result[0]==md5(strtolower($str)))
				return true;			
		}
		else 
		{			
			if($result == md5(strtolower($str)))
				return true;			
		}
		return false;
	}
	
	public function optimiseDomain($domain)
	{
		$str = str_replace("https://","",$domain);
		return str_replace("http://","",$str);
	}
	
	public function disableConfig($module_name)
	{
		$module_name=$this->getModuleRealKey($module_name);				
		$moduleconfig=$this->getModuleConfig($module_name);
		if(trim($moduleconfig)!="")				
			Mage::getSingleton('core/config')->saveConfig($moduleconfig,0);				
		Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".$module_name,1);		
		Mage::getConfig()->reinit();
	}
	
	public function enableConfig($module_name)
	{
		$module_name=$this->getModuleRealKey($module_name);	
		$moduleconfig=$this->getModuleConfig($module_name);		
		if(trim($moduleconfig)!="")
			Mage::getSingleton('core/config')->saveConfig($moduleconfig,1);
		Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".$module_name,0);
		Mage::getConfig()->reinit();
	}
	
	public function getModuleTypeComment($module)
	{
		$str_mess = "";		
		$type = $this->getModuleType($module);
		$comment = $this->getModuleComment($module);
		
		if(trim($comment)!="")
			$str_mess .= $comment;	
			
		if(trim($type)!="")
			$str_mess .= " Type: ".$type;
			
		return  $str_mess;
	}
		
}