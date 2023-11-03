import './DashBoardOption.css'
import { useEffect, useState } from 'react'

const DashBoardOption = (props) => {

    const [options, setOptions] = useState([])

    useEffect(() => {
        setOptions(props.options.map(option => {
            return <option value={option.val}>{option.display}</option>
        }))
    }, [])

    return (
        <td key={props.btnName} className={props.tdclassName}>

            {props.children}

            <button className="select" name={props.btnName} id={props.btnName}>
                <span>{props.label}<i className="fa-solid fa-angle-down"></i></span>
                <div className="dropdown">
                    {options}
                </div>
            </button>
        </td>
    )
}

export default DashBoardOption