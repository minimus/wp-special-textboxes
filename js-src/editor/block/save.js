import { useBlockProps, RichText } from '@wordpress/block-editor'

const options = stbEditorOptions ?? {}

const save = (props) => {
	const { attributes } = props
	const blockProps = useBlockProps.save()

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

	const { defaults = [], settings = {} } = options ?? {}

	const getCurrentDefaults = (id) => defaults.find((item) => item.slug === id) ?? {}

	const currentDefaults = getCurrentDefaults(styleId)
	const blockCaption = defaultCaption ? currentDefaults.caption : caption
	const blockIcon = currentDefaults?.image?.enabled
		? currentDefaults?.image?.image
		: currentDefaults?.image?.defaultImage
	const blockImage = image === '' ? blockIcon : image

	const getContainerClasses = () => {
		const classes = [
			`stb-style-${styleId}`,
			caption || defaultCaption ? 'stb-caption-box' : '',
			bigImage ?? settings.bigImg ? '' : 'stb-image-small',
			!collapsing ? 'stb-fixed' : '',
			collapsed || settings.collapsed ? 'stb-collapsed' : '',
			settings.side === 0 ? 'stb-no-caption' : '',
		]

		return classes.reduce((acc, curr) => (curr === '' ? acc : [...acc, curr]), ['stb-container']).join(' ')
	}

	const getContainerStyle = () => {
		return {
			...(marginTop ? { marginTop: `${marginTop}px` } : {}),
			...(marginRight ? { marginRight: `${marginRight}px` } : {}),
			...(marginBottom ? { marginBottom: `${marginBottom}px` } : {}),
			...(marginLeft ? { marginLeft: `${marginLeft}px` } : {}),
		}
	}

	return (
		<div className={getContainerClasses()} style={getContainerStyle()}>
			<div className="stb-caption">
				<div className="stb-logo">
					<img className="stb-logo__image" alt="img" src={blockImage} />
				</div>
				<div className="stb-caption-content">{blockCaption}</div>
				<div className="stb-tool" />
			</div>
			<div className="stb-content">
				<RichText.Content value={content} tagName="p" {...blockProps} />
			</div>
		</div>
	)
}

export default save
