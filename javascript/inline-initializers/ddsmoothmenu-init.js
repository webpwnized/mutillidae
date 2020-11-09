$(function() {
	ddsmoothmenu.init({
		mainmenuid: "smoothmenu1", //menu DIV id
		orientation: 'v', //Horizontal or vertical menu: Set to "h" or "v"
		classname: 'ddsmoothmenu', //class added to menu's outer DIV
		//customtheme: ["#cccc44", "#cccccc"],
		contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
	});
});
