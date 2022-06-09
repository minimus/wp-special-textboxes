import styled, { createGlobalStyle } from 'styled-components'

export const MainContainer = styled.div`
	display: grid;
	grid-template-areas: 'header' 'content' 'footer';
	grid-template-rows: 60px auto 40px;
	grid-template-columns: auto;
	width: 100%;
`

export const MainRoot = styled.article`
	grid-area: content;
`

export const LoaderRoot = styled.div`
	position: absolute;
	top: 0;
	left: 0;
	z-index: 100;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 100%;
	height: 100%;
	background: rgba(255, 255, 255, 0.4);
`

export const AdminGlobalStyles = createGlobalStyle`
    body .stb-admin-container * {
        font-family: "Roboto Condensed", Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
	.stb-admin-container input {
		min-height: unset;
		padding: 14px;
		color: unset;
		background-color: unset;
		border: none;
		border-radius: unset;
		box-shadow: none;
	}
	
	.stb-admin-container input:focus {
		background-color: unset;
		border: none;
		outline: none;
		box-shadow: none;
	}
`
