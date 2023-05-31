import React from 'react'
import './HeaderSearch.css'

const HeaderSearch = () => {
    return (
        <div className="header_search_part">
            <input type="search" name="header_search" id="header_search"
                placeholder="Vyhledávejte podle třídy, žáka nebo učitele" />
            <button id="search_btn" className="primar_btn">Vyhledat</button>
        </div>
    )
}

export default HeaderSearch
