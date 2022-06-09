import { useBlockProps, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'
import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
	CheckboxControl,
	Button,
	Flex,
	FlexItem,
} from '@wordpress/components'

const options = window?.stbEditorOptions ?? {}

const Edit = ({ attributes, setAttributes, className }) => {
	const props = { ...useBlockProps() }
	const { list: styleList = [], strings = {} } = options ?? {}

	const {
		styleId,
		caption,
		content,
		defaultCaption,
		bigImage,
		collapsed,
		collapsing,
		image,
		marginTop,
		marginRight,
		marginBottom,
		marginLeft,
	} = attributes

	const {
		blockHeader = 'Special Text',
		colorSchemeLabel = 'Color Scheme',
		captionLabel = 'Caption',
		defaultCaptionLabel = 'Default Caption',
		contentLabel = 'Content',
		imageLabel = 'Image',
		selectImageCaption = 'Select',
		bigImageLabel = 'Big image (only for blocks without caption)',
		appearanceLabel = 'Appearance',
		collapsingLabel = 'Can fold/unfold',
		collapsedLabel = 'Block is folded on loading',
		marginsLabel = 'Margins',
		marginTopLabel = 'Top, px',
		marginRightLabel = 'Right, px',
		marginBottomLabel = 'Bottom, px',
		marginLeftLabel = 'Left, px',
	} = strings

	const onChange = (key) => (value) => {
		setAttributes({ [key]: value })
	}

	const onSelect = (media) => {
		setAttributes({ image: media?.url ?? '' })
	}

	return (
		<Panel header={blockHeader} style={{ padding: 10 }} {...props}>
			<PanelBody title={colorSchemeLabel}>
				<PanelRow className="stb-select-row">
					<Flex>
						<FlexItem isBlock>
							<SelectControl
								className="stb-block-style-select stb-input-100"
								value={styleId}
								options={styleList}
								onChange={onChange('styleId')}
							/>
						</FlexItem>
					</Flex>
				</PanelRow>
			</PanelBody>
			<PanelBody title={captionLabel}>
				<PanelRow>
					<TextControl className="stb-input-100" value={caption} onChange={onChange('caption')} />
				</PanelRow>
				<PanelRow>
					<CheckboxControl
						checked={defaultCaption}
						label={defaultCaptionLabel}
						onChange={onChange('defaultCaption')}
					/>
				</PanelRow>
			</PanelBody>
			<PanelBody title={contentLabel}>
				<PanelRow>
					<RichText className={className} value={content} onChange={onChange('content')} tagName="p" />
				</PanelRow>
			</PanelBody>
			<PanelBody title={imageLabel} initialOpen={false}>
				<PanelRow>
					<Flex gap={3}>
						<FlexItem isBlock>
							<TextControl value={image} onChange={onChange('image')} />
						</FlexItem>
						<FlexItem>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={onSelect}
									allowedTypes="image"
									render={({ open }) => (
										<Button isSmall variant="primary" onClick={open}>
											{selectImageCaption}
										</Button>
									)}
								/>
							</MediaUploadCheck>
						</FlexItem>
					</Flex>
				</PanelRow>
				<PanelRow>
					<CheckboxControl checked={bigImage} label={bigImageLabel} onChange={onChange('bigImage')} />
				</PanelRow>
			</PanelBody>
			<PanelBody title={appearanceLabel} initialOpen={false}>
				<PanelRow>
					<Flex gap={3}>
						<FlexItem>
							<CheckboxControl
								checked={collapsing}
								label={collapsingLabel}
								onChange={onChange('collapsing')}
							/>
						</FlexItem>
						<FlexItem>
							<CheckboxControl
								checked={collapsed}
								label={collapsedLabel}
								onChange={onChange('collapsed')}
							/>
						</FlexItem>
					</Flex>
				</PanelRow>
			</PanelBody>
			<PanelBody title={marginsLabel} initialOpen={false}>
				<PanelRow>
					<Flex gap={3}>
						<FlexItem>
							<TextControl value={marginTop} label={marginTopLabel} onChange={onChange('marginTop')} />
						</FlexItem>
						<FlexItem>
							<TextControl
								value={marginRight}
								label={marginRightLabel}
								onChange={onChange('marginRight')}
							/>
						</FlexItem>
						<FlexItem>
							<TextControl
								value={marginBottom}
								label={marginBottomLabel}
								onChange={onChange('marginBottom')}
							/>
						</FlexItem>
						<FlexItem>
							<TextControl value={marginLeft} label={marginLeftLabel} onChange={onChange('marginLeft')} />
						</FlexItem>
					</Flex>
				</PanelRow>
			</PanelBody>
		</Panel>
	)
}

export default Edit
