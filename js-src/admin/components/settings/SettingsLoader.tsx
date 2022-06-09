import React, { useState } from 'react'
import { Skeleton } from '@mui/material'

const SettingsLoader = () => {
	const [skeletons, setSceletons] = useState(new Array(7).fill(<Skeleton />, 0, 7))
}
