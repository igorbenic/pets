(function($){
    var Fields = {
        types: {},
        init: function init() {
            this.registerEvents();
            if ( pets_fields ) {
                this.types = pets_fields.types;
            }
            if ( $('#pets_field_id').length ) {
                $('#pets_field_type').trigger('change');
            }
        },
        registerEvents: function registerEvents() {
            $( document ).on( 'change', '#pets_field_type', { self: this }, this.typeChanged );
            $( document ).on( 'click', '.pets-add-meta-field', { self: this }, this.addMeta );
            $( document ).on( 'click', '.pets-remove-meta-field', this.removeMeta );
        },
        typeChanged: function typeChanged( e ) {
            var type = $(this).val(),
                self = e.data.self,
                types = self.types;

            if ( types.hasOwnProperty( type ) ) {
                var typeInfo = types[ type ];
                if (
                    typeInfo.template
                    && 'single' !== typeInfo.template
                    && self.templateExists( typeInfo.template ) ) {
                    data = {};
                    var options = $('.field-meta').attr('data-options');
                    if ( options ) {
                        data.options = JSON.parse( options );
                    }
                    self.renderTemplate( typeInfo.template, data );
                } else {
                    $('.pets-form-field').find('.field-meta').html( '' );
                }
            }
        },
        addMeta: function addMeta( e, data ) {
            var parent = $(this).parent(),
                self   = e.data.self,
                holder = parent.find('.pets-field-meta-inputs'),
                type   = $(this).attr('data-type'),
                field  = '',
                data   = data || {};

                if ( self.templateExists( type + '-input' ) ) {
                    var t = wp.template( type + '-input' );
                    field = t( data );
                }

            holder.append( field );
            $( document.body ).triggerHandler( 'pets_fields_meta_added', { type: type, html: field, holder: holder });
        },
        removeMeta: function removeMeta(e) {
            $(this).parent().remove();
        },
        templateExists: function templateExists( template ) {
            template = template || false;
            if ( template ) {
                if ( $( '#tmpl-' + template ).length ) {
                    return true;
                }
            }
            console.error( 'Template: ' + template + ' does not exist! Be sure to add a script template of id #tmpl-' + template );
            return false;
        },
        renderTemplate: function renderTemplate( template, data ) {
            var t = wp.template( template ),
                data = data || {},
                html = t( data );

            $('.pets-form-field').find('.field-meta').html( html );
            $( document.body ).triggerHandler( 'pets_fields_template_rendered', { template: template });
        }
    };

    $(function(){
       Fields.init();
    });
})(jQuery);