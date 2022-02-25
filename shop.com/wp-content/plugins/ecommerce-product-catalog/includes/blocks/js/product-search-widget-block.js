/*!
 impleCode Admin scripts v1.0.0 - 2018-12
 Adds appropriate scripts to admin settings
 (c) 2019 impleCode - https://implecode.com
 */

( function ( blocks, editor, element, components, ServerSideRender ) {
    var el = element.createElement;
    var InspectorControls = editor.InspectorControls;
    var TextControl = components.TextControl;
    //var ServerSideRender = components.ServerSideRender;
    var PanelBody = components.PanelBody;
    blocks.registerBlockType( 'ic-epc/product-search-widget', {
        title: ic_epc_blocks.strings.search_widget,
        icon: 'search',
        category: 'ic-epc-block-cat',
        attributes: {
            title: {
                type: 'string',
                default: ''
            }
        },
        edit( props ) {
            var title = props.attributes.title;

            var attributes = {
                title: props.attributes.title,
            };
            function selectTitle( title ) {
                props.setAttributes( { title: title } );
            }
            var ret = [
                el( InspectorControls, { key: "ic-epc-product-search-widget-block-controls" },
                    el( PanelBody, { title: ic_epc_blocks.strings.settings, className: "ic-panel-body", initialOpen: true },
                        el( TextControl, { label: ic_epc_blocks.strings.select_title, value: title, type: "text", onChange: selectTitle } ), )
                    )
            ];
            ret.push(
                el( ServerSideRender, { key: "ic-epc-product-search-widget-server-side-renderer", block: "ic-epc/product-search-widget", attributes: attributes } )
                );


            return ret;
        },
        save( ) {
            return null;
        }
    } );
}(
    window.wp.blocks,
    window.wp.blockEditor,
    window.wp.element,
    window.wp.components,
    window.wp.serverSideRender
    )
    );