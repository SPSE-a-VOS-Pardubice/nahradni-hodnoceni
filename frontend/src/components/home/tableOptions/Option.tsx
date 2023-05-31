import React from 'react'
import './Option.css'

import { useEffect, useState } from 'react'

const Option = (props) => {

    const [options, setOptions] = useState([])

    useEffect(() => {
        setOptions(props.options.map(option => {
            return <option key={option.val} value={option.val}>{option.display}</option>
        }))
    }, [])

    return (
        <div key={props.btnName} className="form_row">

            {props.children}
            
            <button className="select" name={props.btnName} id={props.btnName}>
                <span>{props.label}<i className="fa-solid fa-angle-down"></i></span>
                <div className="dropdown">
                    {options}
                </div>
            </button>
        </div>
    )
}

export default Option
