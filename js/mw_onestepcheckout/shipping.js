/**
 * 
 */
jQuery(function(){

	if(zip_load()){	
				var val_zipship_before=jQuery('#shipping\\:postcode').val();
				jQuery('#shipping\\:postcode').blur(function(event){
					val=this.value;
					if(val!="" && val_zipship_before!=val){					
						if(jQuery('#shipping\\:country_id').val())						
							updateShippingType();
					}
					val_zipship_before=val;
				});
			}
	
	if(region_load()){
		var val_regionship_before=jQuery('#shipping\\:region').val();
		jQuery('#shipping\\:region').blur(function(event){
			val=this.value;
			if(val!="" && val_regionship_before!=val){					
				if(jQuery('#shipping\\:country_id').val())						
					updateShippingType();
			}
			val_regionship_before=val;
		});
	}
	
	if(city_load()){
		var val_cityship_before=jQuery('#shipping\\:city').val();
		jQuery('#shipping\\:city').blur(function(event){
			val=this.value;
			if(val!="" && val_cityship_before!=val){					
				if(jQuery('#shipping\\:country_id').val())						
					updateShippingType();
			}
			val_cityship_before=val;
		});	
	}
	
	

});