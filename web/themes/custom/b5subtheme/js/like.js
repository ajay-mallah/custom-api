(function($, Drupal, drupalSettings) {
  $('.blog-like').click(function (e) {
    e.preventDefault();
    // Fetching node id.
    let nodeId = drupalSettings.likes.target_node_id;
    // Selecting likes counting object.
    let parentDiv = $(this).parent();
    let likeCountObj = parentDiv.children(".field--name-field-likes").children(".field__item");
    // Current likes count. 
    let currentLikes = parseInt(likeCountObj.html());
    
    // Ajax request to update likes count.
    $.ajax({
      url: '/like-module/like/' + nodeId,
      method: 'POST',
      dataType: 'json',
      success: function (response) {
        likeCountObj.html(currentLikes + 1);
      },
      error: function (xhr, status, errorThrown) {
        // Handle errors.
        alert('Error: ' + errorThrown);
      },
    });
  }); 
})(jQuery, Drupal, drupalSettings);