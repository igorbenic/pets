'use strict';
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
            $( document ).on( 'click', '.pets-add-single-image-field', { self: this }, this.addSingleImage );
            $( document ).on( 'click', '.pets-remove-single-image-field', this.remove_image );
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
                    var data = {};
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
        },
        addSingleImage: function addSingleImage( e ){
            e.preventDefault();
            var gallery_id = $(this).attr('data-gallery'),
                self       = e.data.self,
                gallery    = $( gallery_id ),
                image      = $( gallery_id + '_input' );
            self.show_image_media( gallery, image );
        },
        show_image_media: function show_image_media( $imageContainer, $imageInput ) {
            var file_frame,
                self = this;

            /**
             * If an instance of file_frame already exists, then we can open it
             * rather than creating a new instance.
             */

            if ( undefined !== file_frame ) {

                file_frame.open();

                return;

            }

            /**
             * If we're this far, then an instance does not exist, so we need to
             * create our own.
             *
             * Here, use the wp.media library to define the settings of the Media
             * Uploader. We're opting to use the 'post' frame which is a template
             * defined in WordPress core and are initializing the file frame
             * with the 'insert' state.
             *
             * We're also not allowing the user to select more than one image.
             */
            file_frame =  wp.media({
                multiple: false,
            });

            file_frame.on('open',function() {
                var selection = file_frame.state().get('selection');
                var ids = $imageInput.val().split(',');
                ids.forEach(function(id) {
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
            });

            // When an image is selected in the media frame...
            file_frame.on( 'select', function() {

                // Get media attachment details from the frame state
                var attachments = file_frame.state().get('selection').toJSON();

                var attachmentIDs = [];
                $imageContainer.empty();
                var $galleryID = $imageContainer.attr("id");
                for( var i = 0; i < attachments.length; i++ ) {
                    if( attachments[ i ].type == "image" ) {
                        attachmentIDs.push( attachments[ i ].id );
                        $imageContainer.append( self.create_image_field( attachments[ i ], $galleryID ) );
                    }
                }

                $imageInput.val( attachmentIDs.join() );
            });

            // Now display the actual file_frame
            file_frame.open();

        },
        create_image_field: function create_image_field( $attachment, $galleryID ) {
            var image_url = '';


            if( $attachment.sizes.thumbnail ) {
                image_url = $attachment.sizes.thumbnail.url;
            } else {
                image_url = $attachment.sizes.full.url;
            }
            var $output = '<li tabindex="0" role="checkbox" aria-label="' + $attachment.title + '" aria-checked="true" data-id="' + $attachment.id + '" class="attachment save-ready selected details">';
            $output += '<div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait">';
            $output += '<div class="thumbnail">'

            $output += '<div class="centered">'
            $output += '<img src="' + image_url + '" draggable="false" alt="">'
            $output += '</div>'

            $output += '</div>'

            $output += '</div>'

            $output += '<button type="button" data-gallery="#' + $galleryID + '" class="button-link check pets-remove-single-image-field" tabindex="0"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>'


            $output += '</li>';
            return $output;

        },
        remove_image: function remove_image( e ) {
            e.preventDefault();
            var $id = $(this).parent().attr("data-id"),
                $gallery = $(this).attr("data-gallery"),
                $imageInput = $( $gallery + "_input" );

            $(this).parent().remove();

            var ids = $imageInput.val().split(',');

            var $idIndex = ids.indexOf( $id );
            if( $idIndex >= 0 ) {
                ids.splice( $idIndex, 1 );
                $imageInput.val( ids.join() );
            }
        }
    };

    $(function(){
       Fields.init();
    });
})(jQuery);