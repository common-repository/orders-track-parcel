function remove_tracking_parcel(id, obj){
    jQuery('#tracking_parcel_fields div#parcel_overlay').show();
    jQuery.post(ajaxurl, {
        'id' : id,
        'order_id' : jQuery('#tracking_parcel_fields input[name=order_id]').val(),
        'action' : 'remove_tracking_parcel'
    },
    function(data){
        jQuery('.tracking-list').html(data);
        jQuery('#tracking_parcel_fields div#parcel_overlay').hide();
    });
    
    return false;
}

jQuery(document).ready(function(){
    jQuery('#tracking_parcel_fields input[type=button]').on('click', function(obj){
        obj.preventDefault();
        var data = {};
        jQuery('#tracking_parcel_fields div#parcel_overlay').show();
        jQuery('#tracking_parcel_fields input').each(function(){
            var field = jQuery(this);
            
            if('notify' == field.attr('name') && !field.is(':checked')){
                return;
            }
            
            data[field.attr('name')] = field.val();
        });
        
        data['action'] = 'add_tracking_parcel';
        
        jQuery.post(ajaxurl, data, function(data){
            jQuery('.tracking-list').html(data);
            jQuery('#tracking_parcel_fields div#parcel_overlay').hide();
        });
    });
});