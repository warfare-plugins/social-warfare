
import './style.scss';
import './editor.scss';

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
registerBlockType( 'social-warfare/social-warfare', {
	title: __( 'Social Warfare' ), // Block title.
	icon: <i className="mce-ico mce-i-sw sw sw-social-warfare" />,
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		// Has a limit of 3 keywords.
		__( 'sharing' ),
		__( 'social sharing' ),
		__( 'share buttons' )
	],
	attributes: {
	   hasFocus: { type: 'boolean', defualt: false },		//* Used only for editor to display either slim or full block.
	   useThisPost: { type: 'string', default: "this" },	//* Option to use share data from this post, or another post.
	   postID: { type: 'number', default: ''},              //* If ${useThisPost} == 'other', the ID of target post to fetch data from.
	   buttons: { type: 'string', default: '' },			//* A csv of valid networks to display in the shortcode.
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
		const { useThisPost, buttons, postID } = props.attributes;

		const updateWhichPost = ( event ) => {
			props.setAttributes( {useThisPost: event.target.value} );
		}

		const updateButtonsList = ( event ) => {
			props.setAttributes( {buttons: event.target.value} );
		}

		const updatePostID = ( event ) => {
            const postID = getCurrentPostId();
			const value = event.target.value;

			if ( value == '' ) {
				props.setAttributes( { postID: "" } )
				return;
			}

			if ( isNaN( parseInt( value ) ) ) {
				return;
			}

			props.setAttributes( { postID: parseInt(value) } )
		}

		return (
			<div className={ `${props.className} social-warfare-block-wrap` }>
			    <div className="head">
				    <p>Social Warfare Shortcode</p>
					<Dashicon className="swp-dashicon"
							  icon="arrow-right"
							  onClick={toggleFocus}
					/>
				</div>

			    <p>Should the buttons reflect this post, or a a different post?</p>

				<select   value={useThisPost == "other" && postID ? "other" : "this"}
				          onChange={updateWhichPost}
			    >
				  <option value="this">This post</option>
				  <option value="other">Another post</option>
			    </select>

				{
				  props.attributes.useThisPost == "other" &&
				  <div>
					  <p>Which post should we fetch SW settings and shares from?</p>
					  <input type="text"
					         onChange ={updatePostID}
							 value={props.attributes.postID}
					  />
				  </div>
				}

				<p>Which networks should we display? Leave blank to use your global settings. </p>
				<input value={props.attributes.buttons}
				       type="text"
					   onChange={updateButtonsList}
				/>
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
		const buttons = props.attributes.buttons && props.attributes.buttons.length
		                ? `buttons="${props.attributes.buttons}"` : '';

		const postID = props.attributes.useThisPost == "other"
		                ? `id="${props.attributes.postID}"` : '';

		return (
			<div>
				[social_warfare {buttons} {postID}]
			</div>
		);
	},
} );

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
registerBlockType( 'social-warfare/click-to-tweet', {
	title: __( 'Click To Tweet' ), // Block title.
	icon: <i className="mce-ico mce-i-sw sw swp_twitter_icon" />,
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		// Has a limit of 3 keywords.
		__( 'twitter' ),
		__( 'tweet' ),
		__( 'ctt' )
	],
	attributes: {
	   hasFocus: { type: 'boolean', defualt: false },
	   tweetText: { type: 'string', default: "" },					//* The text to display in the popup dialogue.
	   displayText: { type: 'string', default: "" }					//* The text to display in the post content CTT.
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
 		const { tweetText, displayText, theme } = props.attributes;
		const styles = ['Default', 'Send Her My Love', 'Roll With The Changes', 'Free Bird', 'Don\t Stop Believin\'', 'Thunderstruck', 'Livin\' On A Prayer'];
		const focus = props.attributes.hasFocus ? "swp-active-block " : "swp-inactive-block";
		const background = props.attributes.hasFocus ? "violet" : "cyan";
		const twitterIcon = <i className="mce-ico sw swp_twitter_icon" />;

		/**
		 * Local method delcarations.
		 */
 		const updateText = ( event ) => {
 			const attribute = event.target.name;
 			const value = event.target.value;

			props.setAttributes( { [attribute]: value } )
 		}

		const updateTheme = ( event ) => {
            const index = event.target.value;

			if ( parseInt(index) == 0 ) {
				props.setAttributes( {theme: ''} );
			} else {
				props.setAttributes( {theme: index} );
			}
		}

		const toggleFocus = ( event ) => {
			props.setAttributes( {hasFocus: !props.attributes.hasFocus} );
		}

        //* Inactive state
		if ( !props.attributes.hasFocus ) {
		    const text = props.attributes.displayText ? props.attributes.displayText : "No Click To Tweet text is provided.";
			return (
				<div className={ `${props.className} click-to-tweet-block-wrap ${focus}` }>
				    <div className="head">
					    {twitterIcon}
						<div className="swp-preview">{text}</div>
						<Dashicon className="swp-dashicon"
						          icon="arrow-right"
								  onClick={toggleFocus}
					    />
					</div>
	 			</div>
			)
		}

		//* Active state
 		return (
 			<div className={ `${props.className} click-to-tweet-block-wrap ${focus}` }>
                <div className="head">
				    <p className="heading">Click to Tweet</p>
					<Dashicon icon="arrow-down" onClick={toggleFocus} />
				</div>

				<p>Type your tweet as you want it to display <b><em>on Twitter</em></b>:</p>

 				<textarea name="tweetText"
 				          placeholder="Type your tweet. . . "
 				          onChange={updateText}
 						  value={props.attributes.tweetText}
 			     />

				<p>Type your tweet as you want it to display <b><em>on the page</em></b>:</p>

 				 <textarea name="displayText"
 				          placeholder="Type your tweet. . . "
 				          onChange={updateText}
 						  value={props.attributes.displayText}
 				 />

				 <p>Which theme would you like to use for this CTT?</p>

				 <select name="theme"
				         value={theme}
						 onChange={updateTheme}
				 >
				   {
					 styles.map( ( theme, index ) => <option value={index}>{theme}</option> )
				   }
				 </select>
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
		const { tweetText, displayText } = props.attributes;

		const theme = props.attributes.theme ? `style${props.attributes.theme}` : '';

		if (!tweetText) return;

		return (
			<div>
				[click_to_tweet tweet="{tweetText}" quote="{displayText}" theme="{theme}"]
			</div>
		);
	},
} );
