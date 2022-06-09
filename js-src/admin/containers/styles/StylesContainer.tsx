import React, { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import Styles from '../../components/styles/Styles'
import { getStylesData } from '../../redux/modules/styles/actions'
import type { IReducers } from '../../../types/state'
import StylesLoader from '../../components/styles/StylesLoader'

const StylesContainer = (): JSX.Element => {
	const dispatch = useDispatch()

	const filter = useSelector((state: IReducers) => state.styles.filter)
	const needReload = useSelector((state: IReducers) => state.styles.needReload)
	const loading = useSelector((state: IReducers) => state.styles.loading)

	useEffect(() => {
		if (!loading) {
			getStylesData(filter)(dispatch).then()
		}
	}, [filter, needReload])

	if (loading) return <StylesLoader />

	return <Styles />
}

export default StylesContainer
