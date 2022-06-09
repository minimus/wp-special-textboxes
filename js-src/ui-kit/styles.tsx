import styled from 'styled-components'
import Brightness1Icon from '@mui/icons-material/Brightness1'

const getSize = (val: string): string => {
	switch (val) {
		case 'xSmall':
			return '150px'
		case 'small':
			return '300px'
		case 'half':
			return '50%'
		case 'big':
			return '75%'
		case 'fill':
			return '100%'
		default:
			return '150px'
	}
}

type TRootProps = {
	relative?: boolean
	button?: boolean
	inside?: boolean
	size?: string
}

export const Root = styled.div`
	position: ${(props: TRootProps) => (props.relative ? 'relative' : 'inherit')};
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-start;
	width: ${(props: TRootProps) => (props.inside ? 'inherit' : '100%')};
	margin: 5px 0 10px;

	& .MuiTextField-root {
		width: calc(${(props: TRootProps) => getSize(props.size)} - 10px);
		margin: 5px;
	}

	& .MuiOutlinedInput-adornedEnd {
		padding-right: ${(props: TRootProps) => (props.button ? '0' : '14px')};
	}
`

export const FieldCaption = styled.div`
	color: rgba(0, 0, 0, 0.54);
	font-weight: 700;
`
export const FieldTooltip = styled.p`
	color: rgba(0, 0, 0, 0.54);
	font-weight: 500;
	font-size: 0.75rem;
`

type TRadioGroupRootProps = {
	column: boolean
}

export const RadioGroupRoot = styled.div`
	display: flex;
	flex-direction: ${(props: TRadioGroupRootProps) => (props?.column ? 'column' : 'row')};
`

export const CrossFieldContent = styled.div`
	display: flex;
	flex-direction: column;
	min-width: 380px;
	max-width: 450px;
	margin-top: 10px;
`

export const CrossFieldSingleRow = styled.div`
	display: flex;
	align-items: center;
	justify-content: center;
`

export const CrossFieldDoubleRow = styled.div`
	display: flex;
	align-items: center;
	justify-content: space-between;
`

export const BorderSettingsRoot = styled.div`
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: flex-start;
	margin-top: 10px;
`

export const BorderSettingsContentRow = styled.div`
	display: flex;
	align-items: center;
	justify-content: flex-start;
`

export const ShadowSettingsRoot = styled.div`
	display: grid;
	grid-gap: 20px;
	grid-template-columns: 1fr 250px;
	width: 100%;
	padding: 5px;
`

export const ShadowSettingsContentRoot = styled.div`
	display: flex;
	flex-direction: column;
`

export const ShadowSettingsContentRow = styled.div`
	display: flex;
	align-items: center;
	justify-content: flex-start;
`

export const ShadowSettingsPreviewRoot = styled.div`
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 25px;
	border: 25px solid #dcdada;
`

export const ShadowSettingsPreview = styled.div`
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	width: 100%;
	height: 100%;
	border: 1px solid #dcdada;

	& span:first-child {
		font-weight: 700;
		font-size: 25px;
	}
`

export const ColorPopover = styled.div`
	position: absolute;
	top: 60px;
	left: 10px;
	z-index: 2;

	& .flexbox-fix input {
		padding: initial;
	}
`

export const ColorCover = styled.div`
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
`

type TColorSelectIconProps = {
	value: string
}

export const ColorSelectIcon = styled(Brightness1Icon)`
	&.MuiSvgIcon-root {
		fill: ${(props: TColorSelectIconProps) => props.value};
	}
`

type TImagePreview = {
	preview: number | boolean
}

export const ImageComponentsContainer = styled.div`
	display: ${(props: TImagePreview) => (props.preview ? 'grid' : 'inherit')};
	${(props: TImagePreview) => props.preview && `grid-template-columns: ${props.preview}px 1fr;`}
	grid-gap: 5px;
	width: 100%;
`

export const ImageInputsContainer = styled.div`
	display: flex;
	flex-direction: column;
	width: 100%;
`

type TImagePreviewContainer = {
	image: string
	$size: number | boolean
}

export const ImagePreview = styled.div`
	display: ${(props: TImagePreviewContainer) => (props.$size ? 'inherit' : 'none')};
	max-width: ${(props: TImagePreviewContainer) => (props.$size ? `${props.$size}px` : 0)};
	max-height: ${(props: TImagePreviewContainer) => (props.$size ? `${props.$size}px` : 0)};
	margin: 10px 0 0;
	background-image: url('${(props: TImagePreviewContainer) => props.image}');
	background-repeat: no-repeat;
	background-position: center;
	/* background-size: contain;
	border: 1px solid #dcdada; */
	border-radius: 5px;
`
