

function confirmAction(message, redirection) {
	check = confirm(message);
	if (check) document.location.href = redirection;
}