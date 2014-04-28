$MW_Onestepcheckout(function(){
	if(zip_load()){
			var val_zipbill_before=$MW_Onestepcheckout('#billing\\:postcode').val();
			$MW_Onestepcheckout('#billing\\:postcode').blur(function(event){ 
				val=this.value;
				if(val!="" && val_zipbill_before!=val){					
					if($MW_Onestepcheckout('#billing\\:country_id').val())						
						updateShippingType();
				}
				val_zipbill_before=val;
			});
	}
	
	
	if(region_load()){
	var val_regionbill_before=$MW_Onestepcheckout('#billing\\:region').val();
	$MW_Onestepcheckout('#billing\\:region').blur(function(event){ 
		val=this.value;
		if(val!="" && val_regionbill_before!=val){					
			if($MW_Onestepcheckout('#billing\\:country_id').val())						
				updateShippingType();
		}
		val_regionbill_before=val;
	});	
	}
	
	if(city_load()){
		var val_citybill_before=$MW_Onestepcheckout('#billing\\:city').val();	
		
		$MW_Onestepcheckout('#billing\\:city').blur(function(event){
			val=this.value;
			if(val!="" && val_citybill_before!=val){					
				if($MW_Onestepcheckout('#billing\\:country_id').val())						
					updateShippingType();
			}
			
			val_citybill_before=val;
		});
	}
	
	if(valid_vat()){
		var val_vat_before=$MW_Onestepcheckout('#billing\\:taxvat').val();
		$MW_Onestepcheckout('#billing\\:taxvat').blur(function(event){
			if(val_vat_before != this.value)
				{	
					countrycode=$MW_Onestepcheckout("#billing\\:country_id").val();					
					checkVAT(countrycode,this.value);
				}			
			
			val_vat_before=this.value;
		});
		
}
	
});