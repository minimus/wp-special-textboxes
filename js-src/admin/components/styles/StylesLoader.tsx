import React, { useState } from 'react'
import { Skeleton } from '@mui/material'
import { Root, ToolbarRoot } from './styless'

const StylesLoader = (): JSX.Element => {
	const [skeletons] = useState(new Array(7).fill(<Skeleton width="100%" />, 0, 7))

	return (
		<>
			<ToolbarRoot>
				<Skeleton width="100%" />
			</ToolbarRoot>
			<Root>{skeletons.map((skeleton: JSX.Element) => skeleton)}</Root>
		</>
	)
}

export default StylesLoader
