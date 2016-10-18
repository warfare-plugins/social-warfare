jQuery( function( $ )
{
  function update()
  {
    var $this = $( this ),
      $children = $this.closest( 'li' ).children('ul');

    if ( $this.is( ':checked' ) )
    {
      $children.removeClass( 'hidden' );
    }
    else
    {
      $children
        .addClass( 'hidden' )
        .find( 'input' )
        .removeAttr( 'checked' );
    }
  }

  $( '.swpmb-input' )
    .on( 'change', '.swpmb-input-list.collapse :checkbox', update )
    .on( 'clone', '.swpmb-input-list.collapse :checkbox', update );
  $( '.swpmb-input-list.collapse :checkbox' ).each( update );
} );
