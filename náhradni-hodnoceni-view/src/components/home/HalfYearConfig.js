import './HalfYearConfig.css'

const HalfYearConfig = () => {
    return (
        <div className="half_year_config_part">
            <button className="select" name="half_year_config_select" id="half_year_config_select">
                <span>2022/2023 - 1. pololetí<i className="fa-solid fa-angle-down"></i></span>
                <div className="dropdown">
                    <option value="0">2021/2022 - 2. pololetí</option>
                    <option value="1">2022/2023 - 1. pololetí</option>
                    <option value="2">2022/2023 - 2. pololetí</option>
                </div>
            </button>

            <section className="app_colors_meaning col p-0">
                <article id="succeed" className="color_meaning_art">
                    <p>Úspěšně</p>
                    <div className="color_rect"></div>
                </article>
                <article id="failed" className="color_meaning_art">
                    <p>Neúspěšně</p>
                    <div className="color_rect"></div>
                </article>
                <article id="unmarked" className="color_meaning_art">
                    <p>Nehodnoceno</p>
                    <div className="color_rect"></div>
                </article>
            </section>
        </div>
    )
}

export default HalfYearConfig