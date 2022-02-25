/*!
 impleCode Admin scripts v1.0.0 - 2018-12
 Adds appropriate scripts to admin settings
 (c) 2019 impleCode - https://implecode.com
 */

( function ( blocks, editor, element, components, ServerSideRender ) {
    var el = element.createElement;
    var InspectorControls = editor.InspectorControls;
    var TextControl = components.TextControl;
    var CheckboxControl = components.CheckboxControl;
    //var ServerSideRender = components.ServerSideRender;
    var PanelBody = components.PanelBody;

    blocks.registerBlockType( 'ic-epc/product-category-widget', {
        title: ic_epc_blocks.strings.category_widget,
        icon: 'networking',
        category: 'ic-epc-block-cat',
        attributes: {
            title: {
                type: 'string',
                default: ''
            },
            dropdown: {
                type: 'bool',
                default: ''
            },
            count: {
                type: 'bool',
                default: ''
            },
            hierarchical: {
                type: 'bool',
                default: ''
            }
        },
        edit( props ) {
            var title = props.attributes.title;
            if ( props.attributes.dropdown ) {
                var dropdown = true;
            } else {
                var dropdown = false;
            }
            if ( props.attributes.count ) {
                var count = true;
            } else {
                var count = false;
            }
            if ( props.attributes.hierarchical ) {
                var hierarchical = true;
            } else {
                var hierarchical = false;
            }

            var attributes = {
                title: title,
                dropdown: dropdown,
                count: count,
                hierarchical: hierarchical,
            };
            function selectTitle( title ) {
                props.setAttributes( { title: title } );
            }
            function selectDropdown( dropdown ) {
                props.setAttributes( { dropdown: dropdown } );
            }
            function selectCount( count ) {
                props.setAttributes( { count: count } );
            }
            function selectHierarchical( hierarchical ) {
                props.setAttributes( { hierarchical: hierarchical } );
            }

            var ret = [
                el( InspectorControls, { key: "ic-epc-product-categories-widget-block-controls" },
                    el( PanelBody, { title: ic_epc_blocks.strings.settings, className: "ic-panel-body", initialOpen: true },
                        el( TextControl, { label: ic_epc_blocks.strings.select_title, value: title, type: "text", onChange: selectTitle } ),
                        el( CheckboxControl, { label: ic_epc_blocks.strings.select_dropdown, value: "1", checked: dropdown, type: "checkbox", onChange: selectDropdown } ),
                        el( CheckboxControl, { label: ic_epc_blocks.strings.select_count, value: count, type: "checkbox", onChange: selectCount } ),
                        el( CheckboxControl, { label: ic_epc_blocks.strings.select_hierarchical, value: hierarchical, type: "checkbox", onChange: selectHierarchical } ),
                        )
                    )
            ];

            ret.push(
                el( ServerSideRender, { key: "ic-epc-product-categories-widget-server-side-renderer", block: "ic-epc/product-category-widget", attributes: attributes } )
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