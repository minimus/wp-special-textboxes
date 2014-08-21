tinyMCEPopup.requireLangPack();

function init() {
	tinyMCEPopup.resizeToInnerSize();
	
	TinyMCE_EditableSelects.init();
}

function insertWSTBCode() {
	
	var wstbCode;
	
	var f = document.forms[0];
	var radio = f.elements.wstb_collapsed;
  var cRadio = f.elements.wstb_collapsing;
  var mRadio = f.elements.wstb_mode;
  var dRadio = f.elements.wstb_dir;
  var sRadio = f.elements.wstb_shadow;
	
	var wstbID = f.elements.wstb_id.value;
	var wstbCaption = f.elements.wstb_caption.value;
  var wstbDefCap = f.elements.wstb_default_caption.checked;
	var wstbFloat = f.elements.wstb_float.checked;
	var wstbAlign = f.elements.wstb_align.value;
	var wstbWidth = f.elements.wstb_width.value;
	var wstbColor = f.elements.wstb_fcolor.value.replace("#", "");
	var wstbCColor = f.elements.wstb_cfcolor.value.replace("#", "");
	var wstbBGColor = f.elements.wstb_bgcolor.value.replace("#", "");
	var wstbCBGColor = f.elements.wstb_cbgcolor.value.replace("#", "");
  var wstbBGColorTo = f.elements.wstb_bgcolorto.value.replace("#", "");
  var wstbCBGColorTo = f.elements.wstb_cbgcolorto.value.replace("#", "");
	var wstbBColor = f.elements.wstb_bcolor.value.replace("#", "");
  var wstbBWidth = f.elements.wstb_bwidth.value;
	var wstbImage = f.elements.wstb_image_url.value;
	var wstbBigImage = f.elements.wstb_big_image.checked;
	var wstbNoImage = f.elements.wstb_noimage.checked;
  var wstbLeftMargin = f.elements.wstb_left_margin.value;
  var wstbRightMargin = f.elements.wstb_right_margin.value;
  var wstbTopMargin = f.elements.wstb_top_margin.value;
  var wstbBottomMargin = f.elements.wstb_bottom_margin.value;
  var wstbCollapsing = 0;
	var wstbCollapsed = 0;
  var wstbMode = 0;
  var wstbDir = 0;
  var wstbShadow = 0;
  
	if(cRadio[0].checked) wstbCollapsing = 1;
  else if(cRadio[1].checked) wstbCollapsing = 2;
  
  if(radio[0].checked) wstbCollapsed = 1;
	else if(radio[1].checked) wstbCollapsed = 2;
  
  if(mRadio[0].checked) wstbMode = 1;
  else if(mRadio[1].checked) wstbMode = 2;
  
  if(dRadio[0].checked) wstbDir = 1;
  else if(dRadio[1].checked) wstbDir = 2;
  
  if(sRadio[0].checked) wstbShadow = 1;
  else if(sRadio[1].checked) wstbShadow = 2;

	var wstbBody =  window.tinyMCE.activeEditor.selection.getContent();
	
	wstbCode = ' [stextbox id="' + wstbID + '"'; 
	if (wstbCaption != '' || wstbDefCap) { 
		if(wstbDefCap) wstbCode += ' defcaption="true"';
    else wstbCode += ' caption="' + wstbCaption + '"';
		
    if (wstbCollapsing == 1) wstbCode += ' collapsing="true"';
    else if(wstbCollapsing == 2) wstbCode += ' collapsing="false"';
    
    if ((wstbCollapsed == 1) && (wstbCollapsing != 2)) wstbCode += ' collapsed="true"';
		else if(wstbCollapsed == 2) wstbCode += ' collapsed="false"';
	}
  if(wstbMode == 1) wstbCode += ' mode="css"';
  else if(wstbMode == 2) wstbCode += ' mode="js"';
  if(wstbDir == 1) wstbCode += ' direction="ltr"';
  else if(wstbDir == 2) wstbCode += ' direction="rtl"';
  if(wstbShadow == 1) wstbCode += ' shadow="true"';
  else if(wstbShadow == 2) wstbCode += ' shadow="false"';
	if (wstbFloat) {
		wstbCode += ' float="true"';
		if (wstbAlign != 'left') wstbCode += ' align="right"';
		if (wstbWidth != '') wstbCode += ' width="' + wstbWidth + '"';
	}
  if (wstbBWidth != '') wstbCode += ' bwidth="' + wstbBWidth + '"';
	if (wstbColor != '') wstbCode += ' color="' + wstbColor + '"';
	if (wstbCColor != '') wstbCode += ' ccolor="' + wstbCColor + '"';
	if (wstbBColor != '') wstbCode += ' bcolor="' + wstbBColor + '"';
	if (wstbBGColor != '') wstbCode += ' bgcolor="' + wstbBGColor + '"';
	if (wstbCBGColor != '') wstbCode += ' cbgcolor="' + wstbCBGColor + '"';
  if (wstbBGColorTo != '') wstbCode += ' bgcolorto="' + wstbBGColorTo + '"';
  if (wstbCBGColorTo != '') wstbCode += ' cbgcolorto="' + wstbCBGColorTo + '"';
  if (wstbLeftMargin != '') wstbCode += ' mleft="' + wstbLeftMargin + '"';
  if (wstbRightMargin != '') wstbCode += ' mright="' + wstbRightMargin + '"';
  if (wstbTopMargin != '') wstbCode += ' mtop="' + wstbTopMargin + '"';
  if (wstbBottomMargin != '') wstbCode += ' mbottom="' + wstbBottomMargin + '"';
	if ((wstbImage != '') & !wstbNoImage) wstbCode += ' image="' + wstbImage + '"';
	if (wstbBigImage) wstbCode += ' big="' + wstbBigImage.toString() + '"';
	if (wstbNoImage) wstbCode += ' image="null"';
	wstbCode += ']' + wstbBody + '[/stextbox]';
	
	window.tinyMCE.activeEditor.execCommand('mceInsertContent', false, wstbCode);
	tinyMCEPopup.editor.execCommand('mceRepaint');
	tinyMCEPopup.close();
	return;
}

tinyMCEPopup.onInit.add(init);