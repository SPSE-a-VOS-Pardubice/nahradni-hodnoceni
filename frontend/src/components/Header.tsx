import './Header.css'

const Header = () => {

    return (
        <header className="header_component">
            <div className="logo">
                <img src="/img/spse_Logo.png" alt="Logo SPŠE a VOŠ Pardubice" />
                <h2>SPŠE a VOŠ Pardubice</h2>
            </div>

            <div className="user_info">
                <img
                    src="https://wp-themes.com/wp-content/themes/zeever/assets/img/man-person-people-hair-photography-summer-1177664-pxhere.com.webp"
                    alt="Profil Mgr. František Věcek" />
                <p>Mgr. František Věcek</p>
                <button className="log_out_btn">
                    <a href="/login">
                        <i className="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </button>
            </div>
        </header>
    )
}

export default Header
