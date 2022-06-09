import styled from 'styled-components'

export const Root = styled.div`
	display: grid;
	grid-gap: 15px;
	grid-template-columns: 1fr 1fr 1fr;
	min-height: 200px;
	margin: 15px;

	@media (max-width: 1400px) {
		grid-template-columns: 1fr 1fr;
	}

	@media (max-width: 1000px) {
		grid-template-columns: 1fr;
	}
`

export const ToolbarRoot = styled.div`
	margin: 15px;

	& .MuiTypography-body1 {
		font-family: 'Roboto Condensed', Arial, Helvetica Neue, Helvetica, sans-serif;
	}
`

export const ItemContent = styled.div`
	display: flex;
	justify-content: space-between;
	width: 100%;
`

export const ItemBody = styled.div`
	display: flex;
	flex-direction: column;
	width: 100%;
	/* padding: 10px; */
`

export const ItemAlias = styled.span`
	font-size: 1rem;
`

export const ItemInfo = styled.span`
	font-size: 13px;
`

export const ItemToolbar = styled.div`
	display: grid;
	grid-gap: 3px;
	grid-template-rows: 25px 25px 25px;
	grid-template-columns: 25px;
`
