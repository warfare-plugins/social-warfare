
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
		const styles = ['Default', 'Send Her My Love', 'Roll With The Changes', 'Free Bird', 'Don\'t Stop Believin\'', 'Thunderstruck', 'Livin\' On A Prayer'];
		const twitterIcon = <i className="mce-ico sw swp_twitter_icon" />;
		const characterLimit = 280;

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
				<div className={ `${props.className} click-to-tweet-block-wrap swp-inactive-block` }>
				    <div className="head" onClick={toggleFocus}>
					    {twitterIcon}
						<div className="swp-preview">{text}</div>
						<Dashicon className="swp-dashicon"
						          icon="arrow-down"
					    />
					</div>
	 			</div>
			)
		}

		//* Active state
 		return (
 			<div className={ `${props.className} click-to-tweet-block-wrap swp-active-block` }>
                <div className="head" onClick={toggleFocus}>
				    <p >Click to Tweet</p>
					<Dashicon className="swp-dashicon"
							  icon="arrow-up"
					/>
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
