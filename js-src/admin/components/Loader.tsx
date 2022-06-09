import React, { useEffect, useState } from 'react'
import { useSelector } from 'react-redux'
import CircularProgress from '@mui/material/CircularProgress'
import { LoaderRoot } from '../styles'
import type { IReducers } from '../../types/state'

const Loader = () => {
	const [progress, setProgress] = useState(0)

	const stylesLoading = useSelector((state: IReducers) => state.styles.loading)
	const settingsLoading = useSelector((state: IReducers) => state.settings.loading)

	const loading = stylesLoading || settingsLoading

	useEffect(() => {
		function tick() {
			// reset when reaching 100%
			setProgress((oldProgress) => (oldProgress >= 100 ? 0 : oldProgress + 1))
		}

		const timer = setInterval(tick, 20)
		return () => {
			clearInterval(timer)
		}
	}, [settingsLoading, stylesLoading])

	if (loading) {
		return (
			<LoaderRoot>
				<CircularProgress variant="determinate" value={progress} />
			</LoaderRoot>
		)
	}
	return null
}

export default Loader
