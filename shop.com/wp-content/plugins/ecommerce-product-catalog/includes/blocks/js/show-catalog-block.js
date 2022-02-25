/*!
 impleCode Admin scripts v1.0.0 - 2018-12
 Adds appropriate scripts to admin settings
 (c) 2019 impleCode - https://implecode.com
 */

( function ( blocks, editor, element, components, ServerSideRender ) {
    var el = element.createElement;
    //var InspectorControls = editor.InspectorControls;
    //var SelectControl = components.SelectControl;
    //var TextControl = components.TextControl;
    //var ServerSideRender = components.ServerSideRender;
    //var Panel = components.Panel;
    //var PanelBody = components.PanelBody;
    //var PanelRow = components.PanelRow;
    blocks.registerBlockType( 'ic-epc/show-catalog', {
        title: ic_epc_blocks.strings.show_catalog,
        icon: 'store',
        category: 'ic-epc-block-cat',
        edit(  ) {
            var ret = [ ];
            ret.push(
                el( ServerSideRender, { key: "ic-epc-show-catalog-server-side-renderer", block: "ic-epc/show-catalog" } )
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