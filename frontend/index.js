const URL = 'http://localhost:8887/Word-Spinner/backend/backend.php'
const SHOW_INFO_BUTTON_ID = 'show-related-info'
const SPIN_BUTTON = 'spin-button-id'

const changeInnerHTML = (parent, value) => { parent.innerHTML = value }

const createTableForShowingInfo = ({TWC, PCT, NWC, SRST, ORST}) => {
    const table = `<table class = 'info-table'>
                        <tr class='info-table-tr'>
                            <td>Total Word Count</td>
                            <td>${TWC}</td>
                        </tr>
                        <tr>
                            <td>Number of Word Changed</td>
                            <td>${NWC ? NWC : 0}</td>
                        </tr>
                        <tr>
                            <td>Pecentage of Changed Text</td>
                            <td>${PCT}</td>
                        </tr>
                        <tr>
                            <td>Similarity Rate of Spinned Text</td>
                            <td>${SRST}</td>
                        </tr>
                        <tr>
                            <td>Originality Rate of Spinned Text</td>
                            <td>${ORST}</td>
                        </tr>
                       </table>`
    return table
}
const onSuccessSpinText = (data) => {
    data = JSON.parse(data)
    const showInfoId = document.getElementById(SHOW_INFO_BUTTON_ID)
    const box2 = document.getElementById('box2')
    if(data.errorMessage){
        changeInnerHTML(box2, '')
        changeInnerHTML(showInfoId, data.errorMessage)

        $(showInfoId).show()
        return
    }
    changeInnerHTML(box2, data.finalString)
    if('otherInfo' in data) changeInnerHTML(showInfoId, createTableForShowingInfo(data.otherInfo))
    $(showInfoId).show()
}

const handleSpinButtonClick = (e) => {
    const inputTextVal = document.getElementById('box1').value

    document.getElementById(SHOW_INFO_BUTTON_ID).innerText = 'Please wait while we spin the text'
    $(`#${SPIN_BUTTON}`).prop('disabled', true)
    data = {inputString: inputTextVal}
    fetchSpinnedText(URL, data,onSuccessSpinText,(err)=> console.log(err))
    $(`#${SPIN_BUTTON}`).prop('disabled', false)
}

const fetchSpinnedText = (url, data, onSuccess = ()=>{}, onFailed = ()=> {}) => {
    $.ajax({
        url: url,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        type: 'POST',
        data: data,
        success: onSuccess,
        fail: onFailed })
}

window.onload = () => {
    const spinButton = document.getElementById('spin-button-id')
    spinButton.addEventListener('click', handleSpinButtonClick)
}