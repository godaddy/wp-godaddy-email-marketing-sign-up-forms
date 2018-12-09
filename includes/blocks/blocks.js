// Locale
import './i18n.js';
import gemIcons from './icons';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;

const {
  registerBlockType,
  RichText,
  AlignmentToolbar,
  BlockAlignmentToolbar
} = wp.blocks;

const {
  BlockControls,
  InspectorControls
} = wp.editor;

const {
  Toolbar,
  SelectControl
} = wp.components;

/**
 * Register block
 */
export default registerBlockType( 'godaddy-email-marketing-sign-up-forms/gem-block', {
  title: __( 'GoDaddy Email Marketing', 'godaddy-email-marketing-sign-up-forms' ),
  description: __( 'Display a GoDaddy email marketing form.', 'godaddy-email-marketing-sign-up-forms' ),
  category: 'widgets',
  icon: gemIcons.mail,
  keywords: [
    __( 'GoDaddy', 'godaddy-email-marketing-sign-up-forms' ),
    __( 'Email', 'godaddy-email-marketing-sign-up-forms' ),
    __( 'Form', 'godaddy-email-marketing-sign-up-forms' ),
  ],

  attributes: {
    title: {
      type: 'string',
      source: 'text',
      selector: '.gem-title',
    },
    form: {
      type: 'array',
      selector: '.form',
      default: ( Object.keys( gem.forms ).length > 0 ) ? gem.forms[0].value : undefined,
    },
  },

  edit: props => {

    const { attributes: { title, form }, isSelected, className, setAttributes } = props;

    return [

      // Admin Block Markup
      <div className={ className } key={ className }>
        <div className="gem-forms">
          { isSelected ? (
            getFormSelect( form, setAttributes )
          ) : ( <div className="gem-form">{ renderGemForm( form ) }</div> ) }
        </div>
      </div>

    ];
  },

  save: props => {
    const { attributes: { title, form }, className } = props;

    return (
      <div>{ renderGemForm( form ) }</div>
    );
  },
} );

function getFormSelect( form, setAttributes ) {

  if ( Object.keys( gem.forms ).length <= 0 ) {

    return <div>{ __( 'GoDaddy Email Marketing is not connected.', 'godaddy-email-marketing-sign-up-forms' ) } <a href={ gem.settingsURL }> {__( 'Connect Now', 'godaddy-email-marketing-sign-up-forms' ) }</a></div>;

  }

  return <SelectControl
    label={ __( 'Gem Form', 'godaddy-email-marketing-sign-up-forms' ) }
    value={ form }
    options={ gem.forms }
    onChange={ ( form ) => { setAttributes( { form } ) } }
  />

}

function renderGemForm( formID ) {

  var data = {
    'action': 'get_gem_form',
    'formID': formID,
  };

  $.post( ajaxurl, data, function( response ) {

    if ( ! response.success ) {

      $( '.gem-form' ).html( gem.getFormError );

      return;

    }

    $( '.gem-form' ).html( response.data );

  } );

}
