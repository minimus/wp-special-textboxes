import React from 'react'
import { useSelector } from 'react-redux'
import type { TElementColors, TImageSettings, ILocale } from '../../../../types/admin'
import EditorSection from './EditorSection'
import { EditorSectionContentRow } from '../styles'
import { ColorsInput } from '@minimus/simplelib-ui-kit'
import ImageParams from '../../../../ui-kit/ImageParams'
import WpMediaButton from '../../../../ui-kit/buttons/WpMediaButton/WpMediaButton'
import { IReducers } from '../../../../types/state'

type TProps = {
	border: TElementColors
	image: TImageSettings
	onBorderChange: (val: Record<string, unknown>) => void
	onImageChange: (val: Record<string, unknown>) => void
}

const BorderImageSection = (props: TProps): JSX.Element => {
	const { border, image, onBorderChange, onImageChange } = props

	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const {
		editor: {
			borderImageSection: {
				caption: captionString = '',
				colorCaption = '',
				colorTooltip = '',
				imageTitle = '',
				imageCaption = '',
				imageCheckCaption = '',
				imageTooltip = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const { color } = border
	const { image: imageUrl, defaultImage, enabled } = image

	const onBorderValueChange =
		(key: string) =>
		(value: string): void => {
			if (onBorderChange) {
				onBorderChange({ [key]: value })
			}
		}

	const onImageValueChange =
		(key: string) =>
		(value: string | boolean): void => {
			if (onImageChange) {
				onImageChange({ [key]: typeof value === 'boolean' ? !value : value })
			}
		}

	return (
		<EditorSection caption={captionString}>
			<EditorSectionContentRow style={{ padding: '5px 0' }}>
				<ColorsInput
					id="border-color"
					value={color}
					caption={colorCaption}
					tooltip={colorTooltip}
					onChange={onBorderValueChange('color')}
				/>
			</EditorSectionContentRow>
			<EditorSectionContentRow>
				<ImageParams
					id="style-icon"
					value={imageUrl}
					check={!enabled}
					defaultImage={defaultImage}
					preview={50}
					title={imageTitle}
					caption={imageCaption}
					checkCaption={imageCheckCaption}
					tooltip={imageTooltip}
					suffix={<WpMediaButton disabled={!enabled} onSelect={onImageValueChange('image')} />}
					size="fill"
					button
					onChange={onImageValueChange('image')}
					onCheck={onImageValueChange('enabled')}
				/>
			</EditorSectionContentRow>
		</EditorSection>
	)
}

export default BorderImageSection
