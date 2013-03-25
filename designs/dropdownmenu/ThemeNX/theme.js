
var cmThemeNXBase = 'ThemeNX/';

// the follow block allows user to re-define theme base directory
// before it is loaded.
try
{
	if (myThemeNXBase)
	{
		cmThemeNXBase = myThemeNXBase;
	}
}
catch (e)
{
}

var cmThemeNX =
{
  	// main menu display attributes
  	//
  	// Note.  When the menu bar is horizontal,
  	// mainFolderLeft and mainFolderRight are
  	// put in <span></span>.  When the menu
  	// bar is vertical, they would be put in
  	// a separate TD cell.

  	// HTML code to the left of the folder item
  	mainFolderLeft: '',
  	// HTML code to the right of the folder item
  	mainFolderRight: '',
	// HTML code to the left of the regular item
	mainItemLeft: '',
	// HTML code to the right of the regular item
	mainItemRight: '',

	// sub menu display attributes

	// HTML code to the left of the folder item
	folderLeft: '',
	// HTML code to the right of the folder item
	folderRight: '',
	// HTML code to the left of the regular item
	itemLeft: '',
	// HTML code to the right of the regular item
	itemRight: '',
	// cell spacing for main menu
	mainSpacing: 0,
	// cell spacing for sub menus
	subSpacing: 0,
	// auto dispear time for submenus in milli-seconds
	delay: 300
};

// horizontal split, used only in sub menus
var cmThemeNXHSplit = [_cmNoClick, '<td colspan="3" style="height: 3px; overflow: hidden"><div class="ThemeNXMenuSplit"></div></td>'];
// vertical split, used only in main menu
var cmThemeNXMainVSplit = [_cmNoClick, '<div class="ThemeNXMenuVSplit"></div>'];
// horizontal split, used only in main menu
var cmThemeNXMainHSplit = [_cmNoClick, '<td colspan="3"><div class="ThemeNXMenuSplit"></div></td>'];
