/*!
 impleCode Admin scripts v1.0.0 - 2018-12
 Adds appropriate scripts to admin settings
 (c) 2019 impleCode - https://implecode.com
 */


( function ( blocks, editor, element, components, ServerSideRender ) {
    var el = element.createElement;
    var InspectorControls = editor.InspectorControls;
    var SelectControl = components.SelectControl;
    var TextControl = components.TextControl;
    //var ServerSideRender = components.ServerSideRender;
    var Panel = components.Panel;
    var PanelBody = components.PanelBody;
    var PanelRow = components.PanelRow;
    blocks.registerBlockType( 'ic-epc/show-categories', {
        title: ic_epc_blocks.strings.show_categories,
        icon: 'networking',
        category: 'ic-epc-block-cat',
        attributes: {
            category: {
                type: 'array',
                default: [ ],
                items: { type: 'integer' }
            },
            orderby: {
                type: 'string',
                default: 'ID'
            },
            order: {
                type: 'string',
                default: 'ASC'
            },
            archive_template: {
                type: 'string',
                default: ic_epc_blocks.archive_template_def
            },
            per_row: {
                type: 'string',
                default: ic_epc_blocks.per_row_def
            }
        },
        edit( props ) {
            var attributes = {
                category: props.attributes.category,
                orderby: props.attributes.orderby,
                order: props.attributes.order,
                archive_template: props.attributes.archive_template,
                per_row: props.attributes.per_row,
            };
            var category = props.attributes.category;
            var orderby = props.attributes.orderby;
            var order = props.attributes.order;
            var archive_template = props.attributes.archive_template;
            var per_row = props.attributes.per_row;

            var categoryOptions = ic_epc_blocks.category_options;
            var orderbyOptions = ic_epc_blocks.category_orderby_options;
            var orderOptions = ic_epc_blocks.order_options;
            var templateOptions = ic_epc_blocks.template_options;
            function selectCategories( categories ) {
                props.setAttributes( { category: categories } );
            }
            function selectOrderby( orderby ) {
                props.setAttributes( { orderby: orderby } );
            }
            function selectOrder( order ) {
                props.setAttributes( { order: order } );
            }
            function selectTemplate( template ) {
                props.setAttributes( { archive_template: template } );
            }
            function selectPerrow( per_row ) {
                props.setAttributes( { per_row: per_row } );
            }
            var ret = [
                el( InspectorControls, { key: "ic-epc-show-categories-block-controls" },
                    el( PanelBody, { title: ic_epc_blocks.strings.by_category, className: "ic-panel-body", initialOpen: category },
                        el( SelectControl, { multiple: true, label: ic_epc_blocks.strings.select_categories, value: category, type: "dropdown", options: categoryOptions, onChange: selectCategories } ),
                        ),
                    el( PanelBody, { title: ic_epc_blocks.strings.sort_limit, className: "ic-panel-body", initialOpen: true },
                        el( SelectControl, { label: ic_epc_blocks.strings.select_orderby, value: orderby, type: "dropdown", options: orderbyOptions, onChange: selectOrderby } ),
                        el( SelectControl, { label: ic_epc_blocks.strings.select_order, value: order, type: "dropdown", options: orderOptions, onChange: selectOrder } ),
                        el( SelectControl, { label: ic_epc_blocks.strings.select_template, value: archive_template, type: "dropdown", options: templateOptions, onChange: selectTemplate } ),
                        el( TextControl, { label: ic_epc_blocks.strings.select_perrow, value: per_row, type: "number", onChange: selectPerrow } ),
                        )
                    )
            ];
            if ( category.length !== 0 ) {
                ret.push(
                    el( ServerSideRender, { key: "ic-epc-show-categories-server-side-renderer", block: "ic-epc/show-categories", attributes: attributes } )
                    );
            } else {
                ret.push(
                    el( Panel, { header: ic_epc_blocks.strings.choose_categories, key: "ic-epc-show-categories-block-panel" },
                        el( PanelBody, { title: ic_epc_blocks.strings.by_category, className: "ic-panel-body", initialOpen: false },
                            el( PanelRow, { },
                                el( SelectControl, { multiple: true, label: ic_epc_blocks.strings.select_categories, value: category, type: "dropdown", options: categoryOptions, onChange: selectCategories } ),
                                )
                            )
                        )
                    );
            }

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