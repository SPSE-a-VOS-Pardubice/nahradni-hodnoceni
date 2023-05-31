import './TableOptions.css'
import Option from './tableOptions/Option'


const finishedOptions = [
    {
        val: 0,
        display: 'Dokončené'
    }, {
        val: 1,
        display: 'Nedokončené'
    }
]

const nh_ozOptions = [
    {
        val: 0,
        display: 'Náhradní hodnocení (NH)'
    }, {
        val: 1,
        display: 'Opravné zkoušky (OZ)'
    }
]

const successOptions = [
    {
        val: 0,
        display: 'Úspěšně'
    }, {
        val: 1,
        display: 'Neúspěšně'
    }
]

const marksOptions = [
    {
        val: 0,
        display: '1 - Výborný'
    }, {
        val: 1,
        display: '2 - Chvalitebný'
    }, {
        val: 2,
        display: '3 - Dobrý'
    }, {
        val: 3,
        display: '4 - Dostatečný'
    }, {
        val: 4,
        display: '5 - Nedostatečný'
    }
]

const formOptions = [
    {
        val: 0,
        display: 'Odevzdané'
    }, {
        val: 1,
        display: 'Neodevzdané'
    }, {
        val: 2,
        display: 'Pošta'
    }
]

const orderOptions = [
    {
        val: 0,
        display: 'Žáka (A-Z)'
    }, {
        val: 1,
        display: 'Žáka (Z-A)'
    }, {
        val: 2,
        display: 'Učitele (A-Z)'
    }, {
        val: 3,
        display: 'Učitele (Z-A)'
    }, {
        val: 4,
        display: 'Třídy (1. - 4.)'
    }, {
        val: 5,
        display: 'Třídy (4. - 1.)'
    }, {
        val: 6,
        display: 'Známky (1 - 5)'
    }, {
        val: 7,
        display: 'Známky (5 - 1)'
    }
]

const TableOptions = () => {

    return (
        <>
            <div className="table_options_part">
                <div class="view_form">
                    <Option btnName={"finished"} label={"Dokončené"} options={finishedOptions} >
                        <label for="finished">Zobrazit:</label>
                    </Option>

                    <Option btnName={"nh_oz"} label={"Náhradní hodnocení"} options={nh_ozOptions} />
                    <Option btnName={"success"} label={"Úspěšně"} options={successOptions} />
                    <Option btnName={"marks"} label={"Známky"} options={marksOptions} />
                    <Option btnName={"form"} label={"Formulář"} options={formOptions} />

                    <div id="view_delete_btn" className="form_row">
                        <i className="fa-solid fa-x"></i>
                    </div>
                </div>

                <div className="order_by_form">
                    <Option btnName={'sort_className'} label={'Třídy'} options={orderOptions} >
                        <label for="order_by">Seřadit podle:</label>
                    </Option>
                </div>
            </div>
        </>

    )
}

export default TableOptions