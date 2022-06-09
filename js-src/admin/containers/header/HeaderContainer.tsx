import React, { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { ERROR, HEADER_FETCH_DATA, START, SUCCESS } from '../../redux/constants'
import { getData } from '../../redux/helpers'
import Header from '../../components/header/Header'
import type { IReducers } from '../../../types/state'
import type { TDispatch, TSysInfo } from '../../../types/admin'

const HeaderContainer = (): JSX.Element => {
	const sysInfo: TSysInfo = useSelector((state: IReducers) => state.header.sysInfo)
	const dispatch: TDispatch = useDispatch()

	useEffect(() => {
		if (sysInfo === null) {
			dispatch({ type: HEADER_FETCH_DATA + START })

			getData('sysinfo')
				.then((data) => {
					dispatch({
						type: HEADER_FETCH_DATA + SUCCESS,
						payload: data?.data,
					})
				})
				.catch((err: Record<string, unknown>) => {
					dispatch({
						type: HEADER_FETCH_DATA + ERROR,
						payload: err,
					})
				})
		}
	}, [sysInfo, dispatch])

	return <Header />
}

export default HeaderContainer
