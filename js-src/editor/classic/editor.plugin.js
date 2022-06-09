/**
 * Created by minimus on 18.06.2017.
 */
;(function () {
	function createColorPickAction() {
		const ed = tinymce.activeEditor
		const colorPickerCallback = ed.settings.color_picker_callback

		if (colorPickerCallback) {
			return function () {
				const self = this

				colorPickerCallback.call(ed, (value) => self.value(value).fire('change'), self.value())
			}
		}
	}

	const styleList = stbEditorOptions.list

	tinymce.PluginManager.requireLangPack('wstb')

	tinymce.create('tinymce.plugins.wstb', {
		init: function (ed, url) {
			this.editor = ed
			const title = ed.getLang('wstb.title')

			ed.addCommand('wstb', () => {
				const se = ed.selection

				// No selection
				if (se.isCollapsed()) return

				ed.windowManager.open({
					width: 600,
					height: 530,
					inline: 1,
					title: ed.getLang('wstb.title'),
					bodyType: 'tabpanel',
					body: [
						{
							type: 'form',
							name: 'wstb_tab_general',
							title: ed.getLang('wstb.basic_tab'),
							minWidth: 558,
							items: [
								{
									type: 'listbox',
									name: 'wstb_id',
									label: ed.getLang('wstb.box_id'),
									values: styleList,
								},
								{
									type: 'textbox',
									name: 'wstb_caption',
									label: ed.getLang('wstb.caption'),
								},
								{
									type: 'checkbox',
									name: 'wstb_default_caption',
									text: ed.getLang('wstb.default_caption'),
									checked: false,
								},
								{
									type: 'label',
									name: 'collapsing_label',
									text: ed.getLang('wstb.block_collapsing'),
								},
								{
									type: 'listbox',
									name: 'wstb_collapsing',
									values: [
										{ text: ed.getLang('wstb.default'), value: 'default', selected: 1 },
										{ text: ed.getLang('wstb.yes'), value: 'yes' },
										{ text: ed.getLang('wstb.no'), value: 'no' },
									],
								},
								{
									type: 'label',
									name: 'collapsed_label',
									text: ed.getLang('wstb.collapsed'),
								},
								{
									type: 'listbox',
									name: 'wstb_collapsed',
									values: [
										{ text: ed.getLang('wstb.default'), value: 'default', selected: 1 },
										{ text: ed.getLang('wstb.yes'), value: 'yes' },
										{ text: ed.getLang('wstb.no'), value: 'no' },
									],
								},
								{
									type: 'label',
									name: 'mode_label',
									text: ed.getLang('wstb.drawing_mode'),
								},
								{
									type: 'listbox',
									name: 'wstb_mode',
									values: [
										{ text: ed.getLang('wstb.default'), value: 'default', selected: 1 },
										{ text: 'CSS', value: 'css' },
										{ text: 'JavaScript', value: 'js' },
									],
								},
								{
									type: 'label',
									name: 'dir_label',
									text: ed.getLang('wstb.direction'),
								},
								{
									type: 'listbox',
									name: 'wstb_dir',
									values: [
										{ text: ed.getLang('wstb.default'), value: 'default', selected: 1 },
										{ text: ed.getLang('wstb.left_to_right'), value: 'ltr' },
										{ text: ed.getLang('wstb.right_to_left'), value: 'rtl' },
									],
								},
								{
									type: 'label',
									name: 'dir_label',
									text: ed.getLang('wstb.shadow'),
								},
								{
									type: 'listbox',
									name: 'wstb_shadow',
									values: [
										{ text: ed.getLang('wstb.default'), value: 'default', selected: 1 },
										{ text: ed.getLang('wstb.enable'), value: 'true' },
										{ text: ed.getLang('wstb.disable'), value: 'false' },
									],
								},
							],
						},
						{
							type: 'form',
							name: 'wstb_tab_extended',
							title: ed.getLang('wstb.extended_tab'),
							minWidth: 558,
							items: [
								{
									type: 'label',
									name: 'image_label',
									text: ed.getLang('wstb.image'),
								},
								{
									type: 'textbox',
									name: 'wstb_image_url',
									label: ed.getLang('wstb.image_url'),
								},
								{
									type: 'checkbox',
									name: 'wstb_big_image',
									text: ed.getLang('wstb.image_big'),
									checked: false,
								},
								{
									type: 'checkbox',
									name: 'wstb_noimage',
									text: ed.getLang('wstb.image_no'),
									checked: false,
								},
								{
									type: 'label',
									name: 'margins_label',
									text: ed.getLang('wstb.margins'),
								},
								{
									type: 'textbox',
									name: 'wstb_top_margin',
									label: ed.getLang('wstb.margin_top'),
								},
								{
									type: 'textbox',
									name: 'wstb_right_margin',
									label: ed.getLang('wstb.margin_right'),
								},
								{
									type: 'textbox',
									name: 'wstb_bottom_margin',
									label: ed.getLang('wstb.margin_bottom'),
								},
								{
									type: 'textbox',
									name: 'wstb_left_margin',
									label: ed.getLang('wstb.margin_left'),
								},
								{
									type: 'label',
									name: 'floating_label',
									text: ed.getLang('wstb.floating_mode_settings'),
								},
								{
									type: 'checkbox',
									name: 'wstb_float',
									text: ed.getLang('wstb.floating_mode'),
									checked: false,
								},
								{
									type: 'listbox',
									name: 'wstb_align',
									label: ed.getLang('wstb.alignment'),
									values: [
										{ text: ed.getLang('wstb.left'), value: 'left', selected: 1 },
										{ text: ed.getLang('wstb.right'), value: 'right' },
									],
								},
								{
									type: 'textbox',
									name: 'wstb_width',
									label: ed.getLang('wstb.box_width'),
								},
							],
						},
						{
							type: 'form',
							name: 'wstb_tab_colors',
							title: ed.getLang('wstb.colors'),
							minWidth: 558,
							items: [
								{
									type: 'label',
									name: 'colors_label',
									text: ed.getLang('wstb.colors'),
								},
								{
									type: 'colorbox',
									name: 'wstb_fcolor',
									label: ed.getLang('wstb.text'),
									onaction: createColorPickAction(),
								},
								{
									type: 'colorbox',
									name: 'wstb_bgcolor',
									label: ed.getLang('wstb.background'),
									onaction: createColorPickAction(),
								},
								{
									type: 'colorbox',
									name: 'wstb_bgcolorto',
									label: ed.getLang('wstb.stop'),
									onaction: createColorPickAction(),
								},
								{
									type: 'colorbox',
									name: 'wstb_cfcolor',
									label: ed.getLang('wstb.caption_text'),
									onaction: createColorPickAction(),
								},
								{
									type: 'colorbox',
									name: 'wstb_cbgcolor',
									label: ed.getLang('wstb.caption_background'),
									onaction: createColorPickAction(),
								},
								{
									type: 'colorbox',
									name: 'wstb_cbgcolorto',
									label: ed.getLang('wstb.caption_stop'),
									onaction: createColorPickAction(),
								},
								{
									type: 'label',
									name: 'border_label',
									text: ed.getLang('wstb.border'),
								},
								{
									type: 'colorbox',
									name: 'wstb_bcolor',
									label: ed.getLang('wstb.border_color'),
									onaction: createColorPickAction(),
								},
								{
									type: 'textbox',
									name: 'wstb_bwidth',
									label: ed.getLang('wstb.border_width'),
									tooltip: ed.getLang('wstb.border_width'),
								},
							],
						},
					],
					buttons: [
						{ text: ed.getLang('wstb.insert'), onclick: 'submit' },
						{ text: ed.getLang('wstb.cancel'), onclick: 'close' },
					],
					onsubmit: function (e) {
						const radio = e.data.wstb_collapsed
						const cRadio = e.data.wstb_collapsing
						const mRadio = e.data.wstb_mode
						const dRadio = e.data.wstb_dir
						const sRadio = e.data.wstb_shadow

						const wstbID = e.data.wstb_id
						const wstbCaption = e.data.wstb_caption
						const wstbDefCap = e.data.wstb_default_caption
						const wstbFloat = e.data.wstb_float
						const wstbAlign = e.data.wstb_align
						const wstbWidth = e.data.wstb_width
						const wstbColor = e.data.wstb_fcolor.replace('#', '')
						const wstbCColor = e.data.wstb_cfcolor.replace('#', '')
						const wstbBGColor = e.data.wstb_bgcolor.replace('#', '')
						const wstbCBGColor = e.data.wstb_cbgcolor.replace('#', '')
						const wstbBGColorTo = e.data.wstb_bgcolorto.replace('#', '')
						const wstbCBGColorTo = e.data.wstb_cbgcolorto.replace('#', '')
						const wstbBColor = e.data.wstb_bcolor.replace('#', '')
						const wstbBWidth = e.data.wstb_bwidth
						const wstbImage = e.data.wstb_image_url
						const wstbBigImage = e.data.wstb_big_image
						const wstbNoImage = e.data.wstb_noimage
						const wstbLeftMargin = e.data.wstb_left_margin
						const wstbRightMargin = e.data.wstb_right_margin
						const wstbTopMargin = e.data.wstb_top_margin
						const wstbBottomMargin = e.data.wstb_bottom_margin

						const wstbCollapsing = cRadio === 'default' ? 0 : cRadio === 'yes' ? 1 : 2
						const wstbCollapsed = radio === 'default' ? 0 : radio === 'yes' ? 1 : 2
						const wstbShadow = sRadio === 'default' ? 0 : sRadio === 'enabled' ? 1 : 2

						// Caption
						let caption = ''
						if (wstbCaption !== '' || wstbDefCap) {
							if (wstbDefCap) caption = ' defcaption="true"'
							else caption = ` caption='${wstbCaption}'`

							if (wstbCollapsing === 1) caption += ' collapsing="true"'
							else if (wstbCollapsing === 2) caption += ' collapsing="false"'

							if (wstbCollapsed === 1 && wstbCollapsing !== 2) caption += ' collapsed="true"'
							else if (wstbCollapsed === 2) caption += ' collapsed="false"'
						}

						// Mode, direction, shadow
						const mode = mRadio !== 'default' ? ` mode='${mRadio}'` : ''
						const dir = dRadio !== 'default' ? ` direction='${dRadio}'` : ''
						let shadow = ''
						if (wstbShadow === 1) shadow = ' shadow="true"'
						else if (wstbShadow === 2) shadow = ' shadow="false"'

						// Float Mode
						let float = ''
						if (wstbFloat) {
							float += ` float="true" align='${wstbAlign}'`
							if (wstbWidth !== '') float += ` width='${wstbWidth}'`
						}

						// Colors
						let colors = ''
						if (wstbBWidth !== '') colors += ` bwidth='${wstbBWidth}'`
						if (wstbColor !== '') colors += ` color='${wstbColor}'`
						if (wstbCColor !== '') colors += ` ccolor='${wstbCColor}'`
						if (wstbBColor !== '') colors += ` bcolor='${wstbBColor}'`
						if (wstbBGColor !== '') colors += ` bgcolor='${wstbBGColor}'`
						if (wstbCBGColor !== '') colors += ` cbgcolor='${wstbCBGColor}'`
						if (wstbBGColorTo !== '') colors += ` bgcolorto='${wstbBGColorTo}'`
						if (wstbCBGColorTo !== '') colors += ` cbgcolorto='${wstbCBGColorTo}'`

						// Margins
						let margins = ''
						if (wstbLeftMargin !== '') margins += ` mleft='${wstbLeftMargin}'`
						if (wstbRightMargin !== '') margins += ` mright='${wstbRightMargin}'`
						if (wstbTopMargin !== '') margins += ` mtop='${wstbTopMargin}'`
						if (wstbBottomMargin !== '') margins += ` mbottom='${wstbBottomMargin}'`

						// Image
						let image = ''
						if (wstbImage !== '' && !wstbNoImage) image += ` image='${wstbImage}'`
						if (wstbBigImage) image += " big='true'"
						if (wstbNoImage) image = " image='null'"

						const wstbAttrs = `${caption}${mode}${margins}${dir}${shadow}${float}${colors}${image}`

						const wstbBody = ed.selection.getContent()

						const wstbCode = `[stextbox id='${wstbID}'${wstbAttrs}]${wstbBody}[/stextbox]`

						ed.insertContent(wstbCode)
					},
				})
			})

			ed.addButton('wstb', {
				title: title, //'Insert Special Text Box',
				cmd: 'wstb',
				image: url + '/img/wstb.png',
			})

			ed.on('NodeChange', () => {
				ed.controlManager.setDisabled('wstb', ed.selection.isCollapsed())
			})
		},

		getInfo: function () {
			return {
				longname: 'Special Text Boxes',
				author: 'minimus',
				authorurl: 'https://blogcoding.ru',
				infourl: 'https://www.simplelib.com',
				version: '6.0.0',
			}
		},
	})

	tinymce.PluginManager.add('wstb', tinymce.plugins.wstb)
})()
