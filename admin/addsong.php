<?php
require_once( 'includes/gob_admin.php' );
require_once( 'includes/admin_header.php' );

mod_check();
?>

<h1>Add New Song</h1>
<form method="post" action="admin_process.php">
	<label>Round</label>
	<input type="text" name="round" />
	<br />
	
	<label>Song Name</label>
	<input type="text" name="name" />
	<br />
	
	<label>Song URL</label>
	<input type="text" name="url" />
	<br />
	
	<label>Lyrics Bandit</label>
	<input type="text" name="lyrics" />
	<br />
	
	<label>Music Bandit</label>
	<input type="text" name="music" />
	<br />
	
	<label>Vocals Bandit</label>
	<input type="text" name="vocals" />
	<br />
	
	<label>Song Lyrics</label>
	<textarea rows="5" cols="20" name="lyricsheet"></textarea>
	<br />
	
	<label>Votes - Song</label>
	<input type="text" name="votes" />
	<br />
	
	<label>Votes - Lyrics</label>
	<input type="text" name="lyricsvote" />
	<br />
	
	<label>Votes - Music</label>
	<input type="text" name="musicvote" />
	<br />
	
	<label>Votes - Vocals</label>
	<input type="text" name="vocalsvote" />
	<br />
	
	<label>Winner</label>
	<input type="checkbox" name="winner" value="Yes" />
	<br />
	
	
	<input type="submit" value="Add Song" name="addSong">
</form>

<?php
require_once( 'includes/admin_footer.php' );
?>