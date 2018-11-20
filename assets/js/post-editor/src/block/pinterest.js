
import '../common.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { getCurrentPostId } = wp.data.select( 'core/editor' );
const Dashicon = wp.components.Dashicon;

/**
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'social-warfare/pinterest', {
	title: __( 'Pinterest Image' ), // Block title.
	icon: '',
	category: 'social-warfare', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		// Has a limit of 3 keywords.
		__( 'social' ),
		__( 'tailwind' ),
		__( 'marketing' ),
	],
	attributes: {
	   hasFocus: { type: 'boolean', defualt: false },
	   id: { type: 'number', default: 0},
	   width: { type: 'number', default: 0 },
	   height: { type: 'number', default: 0 },
	   className: { type: 'string', default: ''},
	   alignment: { type: 'string', default: ''},
   },

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	 edit: function( props ) {
		   const icon = '';
		   console.log('props', props)

		   const toggleFocus = ( event ) => {
	   			props.setAttributes( { hasFocus: !props.attributes.hasFocus } );
	   		}

		   const attributes = ['id', 'width', 'height', 'className', 'alignment']
		   const attributeString = attributes.reduce((string, attr) => {
			    if (!props.attributes[attr]) return string;

				if (props.attributes)

				string += ` ${attr}="${props[attr]}"`

			}, '');

		//* Inactive state
		if ( !props.attributes.hasFocus ) {
			return (
				<div className={ `${props.className} pinterest-block-wrap swp-inactive-block` }>
					<div className="head" onClick={toggleFocus}>
						{icon}
						<div className="swp-preview">[pinterest_image{attributeString}]</div>
						<Dashicon className="swp-dashicon"
								  icon="arrow-down"
						/>
					</div>
				</div>
			);
		}

		//* Active state
		return (
			<div className={ `${props.className} pinterest-block-wrap swp-active-block` }>
				<div className="head" onClick={toggleFocus}>
					<p >Click to Tweet</p>
					<Dashicon className="swp-dashicon"
							  icon="arrow-up"
					/>
				</div>
			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function( props ) {

		return (
			<div>
				[pinterest_image]
			</div>
		);
	},
} );
