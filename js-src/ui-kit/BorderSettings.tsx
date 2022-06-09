import React, { useEffect, useState } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { ListInput, SwitchInput, TextInput, IListValue } from '@minimus/simplelib-ui-kit'
import { TDispatch, ILocale, TSettings } from '../types/admin'
import { IReducers } from '../types/state'
import { BorderSettingsContentRow, BorderSettingsRoot, FieldCaption, FieldTooltip, Root } from './styles'
import { SETTINGS_SET_SETTINGS } from '../admin/redux/constants'

const defaultBorderStyles: Array<IListValue> = [
	{ text: 'Solid', value: 'solid' },
	{ text: 'Dashed', value: 'dashed' },
	{ text: 'Dotted', value: 'dotted' },
	{ text: 'None', value: 'none' },
]

const BorderSettings = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const { borderStyle, roundedCorners, radius } = settings

	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const {
		settings: {
			boxSettings: {
				borderSettings: {
					borderSettingsCaption = '',
					borderSettingsTooltip = '',
					borderStyleCaption = '',
					borderStyleValues = undefined,
					roundedCornersCaption = '',
					borderRadiusCaption = '',
				} = {},
			} = {},
		} = {},
	} = localesData ?? {}

	const [borderStyles, setBorderStyles] = useState<Array<IListValue>>(defaultBorderStyles)

	const onStyleChange = (value: string): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { borderStyle: value } })
	}

	const onCornersChange = (value: boolean): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { roundedCorners: value } })
	}

	const onRadiusChange = (value: number): void => {
		dispatch({ type: SETTINGS_SET_SETTINGS, payload: { radius: value } })
	}

	useEffect(() => {
		if (localesData && borderStyleValues) {
			setBorderStyles(
				defaultBorderStyles.map(
					(item: IListValue): IListValue => ({
						text: borderStyleValues[item.value] ?? item.text,
						value: item.value,
					}),
				),
			)
		}
	}, [localesData])

	return (
		<Root size="fill">
			<FieldCaption style={{ margin: '0 5px' }}>{borderSettingsCaption}</FieldCaption>
			<BorderSettingsRoot>
				<BorderSettingsContentRow>
					<ListInput
						id="borderStyle"
						value={borderStyle}
						caption={borderStyleCaption}
						values={borderStyles ?? defaultBorderStyles}
						onChange={onStyleChange}
					/>
				</BorderSettingsContentRow>
				<BorderSettingsContentRow>
					<SwitchInput
						id="roundedCorners"
						caption={roundedCornersCaption}
						value={roundedCorners}
						size="small"
						onChange={onCornersChange}
					/>
					<TextInput
						value={radius}
						caption={borderRadiusCaption}
						id="radius"
						size="small"
						suffix="px"
						contentType="number"
						disabled={!roundedCorners}
						onChange={onRadiusChange}
					/>
				</BorderSettingsContentRow>
			</BorderSettingsRoot>
			<FieldTooltip>{borderSettingsTooltip}</FieldTooltip>
		</Root>
	)
}

export default BorderSettings
