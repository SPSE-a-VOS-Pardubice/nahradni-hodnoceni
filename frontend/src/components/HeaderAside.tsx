import { useEffect, useState } from 'react'
import './HeaderAside.css'
import React from 'react'

const HeaderAside = (props) => {

    const [button, setButton] = useState(false)

    useEffect(() => {
        setButton(props.useButton)
    }, [])

    return (
        <aside className="header_aside">
            <div className="logo">
                <img src="/img/spse_Logo.png" alt="Logo SPŠE a VOŠ Pardubice" />
                <h2>SPŠE a VOŠ Pardubice</h2>
            </div>

            <div className="user_info">
                <img
                    src="https://wp-themes.com/wp-content/themes/zeever/assets/img/man-person-people-hair-photography-summer-1177664-pxhere.com.webp"
                    alt="Profil Mgr. František Věcek" />
                <p>Mgr. František Věcek</p>
                {button && (
                    <button className="log_out_btn">
                        <a href="/login">
                            <i className="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </button>
                )}
            </div>
        </aside>
    )
}

export default HeaderAside
