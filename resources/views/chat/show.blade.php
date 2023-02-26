<html>
<head>
<title>sample</title>
<script>
var conn = new WebSocket('ws://localhost:8090');

var from_user_id = "{{ Auth::user()->id }}";

var to_user_id = "";

conn.onopen = function(e){

	console.log("Connection established!");

	load_unconnected_user(from_user_id);

	load_unread_notification(from_user_id);

	load_connected_chat_user(from_user_id);

};

// function --------------------------------------------
function load_unconnected_user(from_user_id)
{
	var data = {
		from_user_id : from_user_id,
		type : 'request_load_unconnected_user'
	};

	conn.send(JSON.stringify(data));
}

function load_unread_notification(user_id)
{
	var data = {
		user_id : user_id,
		type : 'request_load_unread_notification'
	};

	conn.send(JSON.stringify(data));

}

function load_connected_chat_user(from_user_id)
{
	var data = {
		from_user_id : from_user_id,
		type : 'request_connected_chat_user'
	};

	conn.send(JSON.stringify(data));
}
</script>
</head>
</html>