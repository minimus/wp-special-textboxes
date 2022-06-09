import React from 'react'
import { useSelector, useDispatch } from 'react-redux'
import { useNavigate } from 'react-router-dom'
import Radio from '@mui/material/Radio'
import RadioGroup from '@mui/material/RadioGroup'
import FormControlLabel from '@mui/material/FormControlLabel'
import Fab from '@mui/material/Fab'
import AddIcon from '@mui/icons-material/Add'
import StyleItem from './StyleItem'
import { STYLES_CHANGE_FILTER } from '../../redux/constants'
import { Root, ToolbarRoot } from './styless'
import type { ILocale, TSettings, TStyle } from '../../../types/admin'
import type { IReducers } from '../../../types/state'
import { GlobalCommonStyles, GlobalCoreStyles, GlobalColorStyles } from '../styles'

const Styles = (): JSX.Element => {
	const dispatch = useDispatch()
	const navigate = useNavigate()

	const styles: TStyle[] = useSelector((state: IReducers) => state.styles.styles)
	const filter: number = useSelector((state: IReducers) => state.styles.filter)
	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const {
		styles: {
			filterAll = '',
			filterActive = '',
			filterTrash = '',
			itemAlias = '',
			itemType = '',
			noData = '',
		} = {},
	} = localesData ?? {}

	const handleChange = (event) => {
		dispatch({ type: STYLES_CHANGE_FILTER, payload: Number(event.target.value) })
	}

	const onNewStyleClick = () => {
		navigate('/editor')
	}

	// @ts-ignore
	return (
		<>
			<GlobalCoreStyles />
			<GlobalCommonStyles settings={settings} />
			<GlobalColorStyles settings={settings} styles={styles ?? []} />
			<ToolbarRoot>
				<RadioGroup aria-label="filter" name="filter" value={filter} onChange={handleChange} row>
					<FormControlLabel
						className="stb-styles-filter-label"
						value={0}
						control={<Radio color="primary" />}
						label={filterAll}
						labelPlacement="end"
					/>
					<FormControlLabel
						className="stb-styles-filter-label"
						value={1}
						control={<Radio color="primary" />}
						label={filterActive}
						labelPlacement="end"
					/>
					<FormControlLabel
						className="stb-styles-filter-label"
						value={2}
						control={<Radio color="primary" />}
						label={filterTrash}
						labelPlacement="end"
					/>
				</RadioGroup>
			</ToolbarRoot>
			<Root>
				{!styles?.length ? (
					<span>{noData}</span>
				) : (
					styles?.map((item: TStyle, idx: number) => (
						<StyleItem
							key={idx.toString()}
							trash={item.trash ? 1 : 0}
							type={item.type}
							slug={item.slug}
							caption={item.caption}
							text={[itemAlias, itemType]}
							colors={item.colors}
							captionBg={!!settings.side}
						/>
					))
				)}
			</Root>
			{!!styles?.length && filter < 2 && (
				<Fab color="primary" style={{ position: 'fixed', bottom: 50, right: 40 }} onClick={onNewStyleClick}>
					<AddIcon />
				</Fab>
			)}
		</>
	)
}

export default Styles
