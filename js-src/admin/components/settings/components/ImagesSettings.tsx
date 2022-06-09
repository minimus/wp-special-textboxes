import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { TextInput, RadioInput, SwitchInput } from '@minimus/simplelib-ui-kit'
import SettingsSection from './SettingsSection'
import { IReducers } from '../../../../types/state'
import { TDispatch, TImageSettings, ILocale, TSettings } from '../../../../types/admin'
import { SETTINGS_SET_SETTINGS } from '../../../redux/constants'
import WpMediaButton from '../../../../ui-kit/buttons/WpMediaButton/WpMediaButton'
import ImageParams from '../../../../ui-kit/ImageParams'

const ImagesSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const {
		core: { yes = 'Yes', no = 'No' } = {},
		settings: {
			imagesSettings: {
				imagesSettingsCaption = '',
				imgMinusTitle = '',
				imgMinusCaption = '',
				imgMinusCheckCaption = '',
				imgMinusTooltip = '',
				imgPlusTitle = '',
				imgPlusCaption = '',
				imgPlusCheckCaption = '',
				imgPlusTooltip = '',
				durationCaption = '',
				durationTooltip = '',
				bigImgCaption = '',
				bigImgTooltip = '',
				showImgCaption = '',
				showImgTooltip = '',
				sideCaption = '',
			} = {},
		} = {},
	} = localesData ?? {}

	const { imgMinus, imgPlus, duration, bigImg, showImg, side } = settings

	const onValueChange =
		(key: string) =>
		(value: string): void => {
			dispatch({ type: SETTINGS_SET_SETTINGS, payload: { [key]: value } })
		}

	const onYesNoChange =
		(key: string) =>
		(value: string): void => {
			dispatch({ type: SETTINGS_SET_SETTINGS, payload: { [key]: value === 'yes' } })
		}

	const onSwitchChange =
		(key: string) =>
		(value: boolean | number): void => {
			dispatch({ type: SETTINGS_SET_SETTINGS, payload: { [key]: value } })
		}

	const onImageChange =
		(parent: string, key: string, data: TImageSettings) =>
		(value: string): void => {
			if (key === 'enabled') {
				dispatch({
					type: SETTINGS_SET_SETTINGS,
					payload: {
						[parent]: { ...data, enabled: !!data.image && !value },
					},
				})
			} else {
				dispatch({
					type: SETTINGS_SET_SETTINGS,
					payload: {
						[parent]: { ...data, [key]: value, enabled: !!value && data.enabled },
					},
				})
			}
		}

	const onImageSelect =
		(parent: string, data: TImageSettings) =>
		(value: string): void => {
			dispatch({
				type: SETTINGS_SET_SETTINGS,
				payload: {
					[parent]: { ...data, image: value, enabled: true },
				},
			})
		}

	return (
		<SettingsSection caption={imagesSettingsCaption}>
			<ImageParams
				id="tool-image-minus"
				value={imgMinus?.image}
				check={!imgMinus?.enabled}
				preview={16}
				defaultImage={imgMinus.defaultImage}
				title={imgMinusTitle}
				caption={imgMinusCaption}
				checkCaption={imgMinusCheckCaption}
				tooltip={imgMinusTooltip}
				suffix={<WpMediaButton onSelect={onImageSelect('imgMinus', imgMinus)} />}
				button
				size="fill"
				onChange={onImageChange('imgMinus', 'image', imgMinus)}
				onCheck={onImageChange('imgMinus', 'enabled', imgMinus)}
			/>
			<ImageParams
				id="tool-image-plus"
				value={imgPlus?.image}
				check={!imgPlus?.enabled}
				preview={16}
				defaultImage={imgPlus.defaultImage}
				title={imgPlusTitle}
				caption={imgPlusCaption}
				checkCaption={imgPlusCheckCaption}
				tooltip={imgPlusTooltip}
				suffix={<WpMediaButton onSelect={onImageSelect('imgPlus', imgPlus)} />}
				button
				size="fill"
				onChange={onImageChange('imgPlus', 'image', imgPlus)}
				onCheck={onImageChange('imgPlus', 'enabled', imgPlus)}
			/>
			<TextInput
				id="duration-value"
				value={duration}
				size="small"
				tooltip={durationTooltip}
				caption={durationCaption}
				contentType="number"
				suffix="ms"
				onChange={onValueChange('duration')}
			/>
			<RadioInput
				id="bigImg"
				value={bigImg ? 'yes' : 'no'}
				values={[
					{ text: yes ?? 'Yes', value: 'yes' },
					{ text: no ?? 'No', value: 'no' },
				]}
				direction="row"
				caption={bigImgCaption}
				tooltip={bigImgTooltip}
				onChange={onYesNoChange('bigImg')}
			/>
			<RadioInput
				id="showImg"
				value={showImg ? 'yes' : 'no'}
				values={[
					{ text: yes ?? 'Yes', value: 'yes' },
					{ text: no ?? 'No', value: 'no' },
				]}
				direction="row"
				caption={showImgCaption}
				tooltip={showImgTooltip}
				onChange={onYesNoChange('showImg')}
			/>
			<SwitchInput id="side" caption={sideCaption} value={side} onChange={onSwitchChange('side')} />
		</SettingsSection>
	)
}

export default ImagesSettings
