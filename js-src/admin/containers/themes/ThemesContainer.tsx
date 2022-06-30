import React, { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { Backdrop } from '@mui/material'
import type { IReducers } from '../../../types/state'
import type { TDispatch } from '../../../types/admin'
import { getThemesInfo } from '../../redux/modules/themes/actions'
import Themes from '../../components/themes/Themes'

const ThemesContainer = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const loading: boolean = useSelector((state: IReducers) => state.themes.loading)

	useEffect(() => {
		if (!loading) {
			void getThemesInfo()(dispatch).then()
		}
	}, [])

	return (
		<>
			<Themes />
			<Backdrop open={loading} sx={{ color: '#fff', zIndex: 1000 }} />
		</>
	)
}

export default ThemesContainer
