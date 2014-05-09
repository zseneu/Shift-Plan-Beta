
function timeoutProcess()
	{	
		val=$MW_Onestepcheckout("#shipping-address-select").val();
		if(val)	
		updateShippingType(add('shipping')); 
	}

 function isInt(x) {
   var y=parseInt(x);
   if (isNaN(y)) return false;
   return x==y && x.toString()==y.toString();
 } 
 
 typeof(id3) == "undefined"
	 
 function add(str){
	var str_val="";
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"-address-select option:selected").val())=='undefined')?'':$MW_Onestepcheckout("#"+str+"-address-select option:selected").val())+",";
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"\\:country_id").val())=='undefined')?'':$MW_Onestepcheckout("#"+str+"\\:country_id").val())+",";
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"\\:postcode").val())=='undefined')?'':$MW_Onestepcheckout("#"+str+"\\:postcode").val())+",";
	if($MW_Onestepcheckout("#"+str+"\\:region_id").attr('display')=='block')
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"\\:region_id").val())=='undefined' && $MW_Onestepcheckout("#"+str+"\\:region_id").val()==null)?"":$MW_Onestepcheckout("#"+str+"\\:region_id").val())+",";
	else
	str_val=str_val+',';
	if($MW_Onestepcheckout("#"+str+"\\:region").attr('display')=='block')
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"\\:region").val())=='undefined' && $MW_Onestepcheckout("#"+str+"\\:region").val()==null)?"":$MW_Onestepcheckout("#"+str+"\\:region").val())+",";
	else
	str_val=str_val+',';
	str_val=str_val+((typeof($MW_Onestepcheckout("#"+str+"\\:city").val())=='undefined')?'':$MW_Onestepcheckout("#"+str+"\\:city").val());
	
	return str_val;
 }
 
$MW_Onestepcheckout(function(){
		
		var flag=1;	
		var i=1;		

		//////////CONFIGURATION
		var shipaddselect=$MW_Onestepcheckout("#shipping-address-select");
		
		////////
		var change=0;		
		var change_select=0; 
		var timer;
		var islogin = logined();	
		var hasadd = hasaddress();	
		
		
		$MW_Onestepcheckout("#billing-address-select").change(function(){			
			if(flag==1){
					change_select=0;
					if(this.value==""){						
						countryid=$MW_Onestepcheckout("#billing\\:country_id option:selected").val();	
						updateBillingForm(this.value,flag);
					}
					else{	
						updateBillingForm(this.value,flag);
					}								
				}				
				else{
					
					updateBillingForm(this.value,flag);
					change_select=1;
				}
			
		});
		
		
		$MW_Onestepcheckout("#shipping-address-select").change(function(){
			
				if(flag==0){	
					change_select=1;		
					if(this.value==""){			
						countryid=$MW_Onestepcheckout("#shipping\\:country_id option:selected").val();						
						if(countryid){		
						
						updateShippingForm(this.value,true); 
						}
					}
					else{						
						val=$MW_Onestepcheckout("#shipping-address-select").val();
						if(val)
						updateShippingForm(this.value,true);			
					}
				}
		});
		
		$MW_Onestepcheckout('#shipping\\:same_as_billing').click(function()
		{			
			if(hasadd)
			{
				address_shipping_id =  $MW_Onestepcheckout("#shipping-address-select").val();
				address_billing_id =  $MW_Onestepcheckout("#billing-address-select").val();			
				if(address_billing_id == "")
					{					
					updateShippingForm(address_shipping_id,false) ;
					}
				else
					{
					updateShippingForm(address_shipping_id,true) ;
					}
			}
			else
			{
				if(flag==0)
					{						
						if(this.checked==false)
							{		
								
								if(!islogin)
									{
										$MW_Onestepcheckout('#shipping-new-address-form').clearForm();
									}
							}
							else
							{
								if(change==1)
								{	
									
									ctid=$MW_Onestepcheckout('#shipping\\:country_id');	
									updateShippingType();
									change=0;
								}
								if(change_select==0){								
									if(!shipaddselect){
										countryid=$MW_Onestepcheckout("#shipping\\:country_id option:selected").val();
										if(countryid!="")									
										updateShippingType();
									}
									change_select=1;
							}
														
							}
					}
				}
		});
		
		$MW_Onestepcheckout("#ship_to_same_address").click(function(){
			 shipaddselect=$MW_Onestepcheckout("#shipping-address-select");
			 billaddselect=$MW_Onestepcheckout("#billing-address-select");
			if(this.checked==false){
			
				$MW_Onestepcheckout("#mw-osc-p2").removeClass('onestepcheckout-numbers onestepcheckout-numbers-2').addClass('onestepcheckout-numbers onestepcheckout-numbers-3');
				$MW_Onestepcheckout("#mw-osc-p3").removeClass('onestepcheckout-numbers onestepcheckout-numbers-3').addClass('onestepcheckout-numbers onestepcheckout-numbers-4');
				$MW_Onestepcheckout("#mw-osc-p4").removeClass('onestepcheckout-numbers onestepcheckout-numbers-4').addClass('onestepcheckout-numbers onestepcheckout-numbers-5');
				
				flag=0;
					if(i==1)
					{
					
						if(!islogin)
							{
						$MW_Onestepcheckout('#shipping-new-address-form').clearForm();  
							}
					i=0;
					}
					
					$MW_Onestepcheckout("#shipping_show").css('display','block');
					this.value=0;		
					if(islogin){

						change_select=1;

						if(change_select==0 || change==0){							
							if(shipaddselect.val()==""){
								if(change==0){
									countryid=$MW_Onestepcheckout("#shipping\\:country_id option:selected").val();
									if(countryid){									
									updateShippingType();
									change=1;
									}
								}
							}
							else{								
							countryid=$MW_Onestepcheckout("#shipping\\:country_id option:selected").val();							
							if(countryid)							
							updateShippingType();
							}
							change_select=1;
						}
					}
					else{
						if(change==0){
							countryid=$MW_Onestepcheckout("#shipping\\:country_id option:selected").val();
							if(countryid){							
							updateShippingType();
							change=1;
							}
						}			
					}
			}
			else{
				// change step order
				$MW_Onestepcheckout("#mw-osc-p2").removeClass('onestepcheckout-numbers onestepcheckout-numbers-3').addClass('onestepcheckout-numbers onestepcheckout-numbers-2');
				$MW_Onestepcheckout("#mw-osc-p3").removeClass('onestepcheckout-numbers onestepcheckout-numbers-4').addClass('onestepcheckout-numbers onestepcheckout-numbers-3');
				$MW_Onestepcheckout("#mw-osc-p4").removeClass('onestepcheckout-numbers onestepcheckout-numbers-5').addClass('onestepcheckout-numbers onestepcheckout-numbers-4');
				
					 flag=1;
					 shipping.setSameAsBilling(true);
					 $('shipping:same_as_billing').checked = false;
					 $MW_Onestepcheckout('#shipping_show').css('display','none');
					 this.value=1;	
					
					if(islogin){
						countryid=$MW_Onestepcheckout("#billing\\:country_id option:selected").val();
						if(countryid){						
						updateShippingType();
						change_select=0;
						}
						if(change_select!=0 ||change==1){	
							if(billaddselect.val()==""){
									if(change==1){
										countryid=$MW_Onestepcheckout("#billing\\:country_id option:selected").val();
										if(countryid){										
										updateShippingType();
										change=0;
										}
									}
								}
								else{
								countryid=$MW_Onestepcheckout("#billing\\:country_id option:selected").val();
								if(countryid)								
								updateShippingType();
								}
								change_select=0;
						}
					}
					else{
						if(change==1){
							countryid=$MW_Onestepcheckout("#billing\\:country_id option:selected").val();
							if(countryid){							
							updateShippingType();
							change=0;
							}
						}
					}
			}
		});
		
		
		$MW_Onestepcheckout('#register_new_account').click(function(){				
				if(this.checked==true){
					$MW_Onestepcheckout('#register-customer-password').css('display','block');
					this.value = 1;
					}
				else{
					this.value = 0;
					$MW_Onestepcheckout('#register-customer-password').css('display','none');
					$MW_Onestepcheckout('#register-customer-password').clearForm();
					}
				});
		
		$MW_Onestepcheckout('#subscribe_newsletter').click(function(){				
				if(this.checked==true){
					this.value = 1;
				}
				else{
					this.value = 0;
				}
		});	
		
		$MW_Onestepcheckout.fn.clearForm=function(){			
			$MW_Onestepcheckout(':input', this).each(function() {					
					var type = $MW_Onestepcheckout(this).get(0).type;	
					var tag = $MW_Onestepcheckout(this).get(0).tagName.toLowerCase();					
					if (type == 'text' || type == 'password' || tag == 'textarea'){
						if((this.id =='billing:postcode' || this.id =='shipping:postcode') && this.value =='.')
							{
							this.value = '';
							}
						if(this.id!='billing:city' && this.id!='billing:taxvat' && this.id!='billing:day' && this.id!='billing:month' && this.id!='billing:year' && this.id!='billing:postcode' && this.id !='billing:region' && this.id!='shipping:city' && this.id!='shipping:postcode' && this.id !='shipping:region' ){
							if((islogin && this.id!='billing:email') || !islogin)
								{
									this.value = '';
								}
						}
						else if(this.value=='n/a'){							
							this.value= '';
						}
					}
					else if ((type == 'checkbox' || type == 'radio') && this.id != 'register_new_account' ){
						
						this.checked = false;
					}
					else if (tag == 'select'){
						if(this.id!='billing:country_id' && this.id!='shipping:country_id' && this.id!='billing:region_id' && this.id!='shipping:region_id'){
							this.selectedIndex = -1;
						}
					}
			});
		};
		
		$MW_Onestepcheckout('#allow_gift_messages').click(function(){
			if (this.checked==true)
				{
				$MW_Onestepcheckout('#allow-gift-message-container').css('display','block');
					if(!islogin)
					{
						$MW_Onestepcheckout('input[id^="gift-message"]').val('');
					}
					else if(!hasadd)
					{
						$MW_Onestepcheckout('input[id^="gift-message-whole-to"]').val('');
						$MW_Onestepcheckout('input[id^="gift-message-"][id$="to"]').val('');
					}
				}
			else
				$MW_Onestepcheckout('#allow-gift-message-container').css('display','none');
		});
	
///////// load ajax 
		// if(country_load()){		
		// 	$MW_Onestepcheckout('#billing\\:country_id').live("change", function(){
		// 		if(valid_vat())
		// 		{			
		// 			val=this.value;	//countryid									
		// 			checkVAT(val,$MW_Onestepcheckout('#billing\\:taxvat').val());				
		// 		}
				
		// 		if(flag==1){						
		// 			updateShippingType();
		// 			change=0;	
		// 			}
		// 		else{
		// 			change=1;		
		// 		}				
		// 	});
			
		// 	$MW_Onestepcheckout('#shipping\\:country_id').live("change", function(){			
		// 			if(flag==0){
		// 			change=1;					
		// 			updateShippingType();
		// 			}
		// 	});
		// }
		
		


		if(region_load()){
			$MW_Onestepcheckout('#billing\\:region_id').live("change", function(){ 
						if(flag==1){							
							updateShippingType();
							change=0;	
							}
						else{
							change=1;		
						}				
				});
				$MW_Onestepcheckout('#shipping\\:region_id').live("change",function(){
						if(flag==0){
						change=1;						
						updateShippingType();
						}
				});
			
		}
});

