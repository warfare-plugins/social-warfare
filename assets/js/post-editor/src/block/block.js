/**
 * BLOCK: post-editor
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: aa Gutenberg Block.
 *
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
registerBlockType( 'social-warfare/post-editor', {
	title: __( 'Social Warfare' ), // Block title.
	icon: <i className="mce-ico mce-i-sw sw sw-social-warfare" />,
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'sharing' ),
		__( 'social sharing' ),
		__( 'share buttons' ),
		// Has a limit of 3 keywords.
		// __( 'facebook' ),
		// __( 'twitter' ),
		// __( 'pinterest' ),
		// __( 'linkedin' ),
	],
	attributes: {
	   whichPost: { type: 'number', default: "this" },
	   networks: { type: 'string', default: '' }
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
		console.log("Props are ", props);

		const updateSelectPost = (event) => {
			event.preventDefault();
			console.log(event.target)
			console.log(event.target.value);
			selectedPost = event.target.value;
		}
		// Creates a <p class='wp-block-cgb-block-post-editor'></p>.
		return (
			<div className={ props.className }>
			    <p>Should the buttons reflect this post, or a a different post?</p>
				<select value={selectedPost} onChange={updateSelectPost}>
				  <option value="this">This post</option>
				  <option value="other">Another post</option>
			    </select>


				{
				  selectedPost == "other" &&
				  <div>
					  <p>Which post sholud we fetch SW settings and shares from?</p>
					  <input type="text" />
				  </div>
				}

				<p>Display these networks: </p>
				<input type="text" />
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
				<p>— Hello from the frontend.</p>
				<p>
					CGB BLOCK: <code>post-editor</code> is a new Gutenberg block.
				</p>
				<p>
					It was created via{ ' ' }
					<code>
						<a href="https://github.com/ahmadawais/create-guten-block">
							create-guten-block
						</a>
					</code>.
				</p>
			</div>
		);
	},
} );
