<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Расписание онлайн</title>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/js/vue@2.js" defer></script>
    <script src="/assets/js/core_v1.3.4.js" defer></script>
    <style>
        .printer{
            width: 21cm;
            height: 29.7cm;
            margin: 0;
            /* change the margins as you want them to be. */
        }
        .printer .tables {
            display: flex;
            flex-wrap: wrap;
        }
        .table-group {
            margin-top: 5px;
            border-collapse: collapse!important;
            border: thin solid black!important;
            text-align: center!important;
        }
        .table-group th, .table-group tr, .table-group td {
            border: thin solid black;
        }
        .download {
            position: fixed;
            width: 100%;
            height: 100vh;
            top: 0;
            left: 0;
            background-color: rgba(0,0,0,0.6);
            color: white;
            font-weight: bold;
            line-height: 100vh;
            font-size: 45px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container" v-if="pages == 'login'">
            <div class="row">
                <div class="col-lg"></div>
                <div class="col-lg-4 col-12">
                    <h2 class="mt-5">Авторизации</h2>
                    <div v-if="forms.f_login.response.type == 'danger'">
                        <div v-for="item in forms.f_login.response.message" class="alert alert-danger" role="alert">
                            @{{ item[0] }}
                        </div>
                    </div>
                    <form>
                        <div class="mb-3">
                            <label for="loginInput" class="form-label">Логин</label>
                            <input type="text"
                                   class="form-control"
                                   id="loginInput"
                                   aria-describedby="loginInput"
                                   v-model="forms.f_login.input.login">
                        </div>
                        <div class="mb-3">
                            <label for="passwordInput" class="form-label">Пароль</label>
                            <input type="password"
                                   class="form-control"
                                   id="passwordInput"
                                   v-model="forms.f_login.input.password">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="inputStayLogged"
                                   v-model="forms.f_login.input.stay">
                            <label class="form-check-label" for="inputStayLogged">Оставаться в системе?</label>
                            <p class="small">Если хотите оставаться в системе всегда</p>
                        </div>
                        <button type="button" class="btn btn-primary" v-on:click="buttonLogin">Войти</button>
                    </form>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'main'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="d-grid gap-2 col-lg-6 col-12 mx-auto">
                    <h2 class="text-center">Выберите раздел</h2>
                    <button type="button" class="btn btn-lg btn-outline-primary" v-on:click="buttonTimetable">Расписание пар</button>
                    <button type="button" class="btn btn-lg btn-outline-dark"  v-on:click="buttonTeachers">Педагоги</button>
                    <button type="button" class="btn btn-lg btn-outline-dark"  v-on:click="buttonSubject">Предметы</button>
                    <button type="button" class="btn btn-lg btn-outline-dark"  v-on:click="buttonGroup">Группы</button>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'teachers'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-6 col-12">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed()"></button>
                    <h1>Педагоги</h1>
                    <div v-if="forms.f_teacher.response.type == 'danger'">
                        <div v-for="item in forms.f_teacher.response.message" class="alert alert-danger" role="alert">
                            @{{ item[0] }}
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text"
                               class="form-control"
                               placeholder="Фамилия Имя Отчество педагога"
                               v-model="forms.f_teacher.input.fullname">
                        <input type="color" class="form-control form-control-color" id="exampleColorInput" v-model="forms.f_teacher.input.color" value="#eb4444" title="Выберите цвет">
                        <button class="btn btn-outline-secondary" type="button" v-on:click="buttonAddTeacher">Создать</button>
                    </div>

                    <div v-if="forms.f_teacher_edit.active" class="border border-danger border-3 rounded-3 p-1">
                        <h6>Редактирование педагога</h6>
                        <div class="input-group mb-3">
                            <input type="text"
                                   class="form-control"
                                   placeholder="Фамилия Имя Отчество педагога"
                                   v-model="forms.f_teacher_edit.fullname">
                            <input type="color" class="form-control form-control-color" id="exampleColorInput" v-model="forms.f_teacher_edit.color" value="#eb4444" title="Выберите цвет">
                            <button class="btn btn-outline-secondary" type="button" v-on:click="buttonEditTeacher">Редактировать</button>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Фамилия Имя Отчество</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in teachers">
                            <th scope="row">@{{ item.id }}</th>
                            <td v-on:dblclick="dbbuttonEditTeacher(item)"><div
                                    style="width: 10px; height: 10px; margin-right: 10px; border-radius: 50%; display: inline-block;"
                                    v-bind:style="{'background-color': item.color}"></div>@{{ item.fullname }}
                            </td>
                            <td>
                                <button class="btn btn-outline-dark" type="button" v-on:click="buttonFirstTeacher(item.id)">Предметы</button>
                            </td>
                        </tr>
                        <tr v-if="teachers.length==0">
                            <th colspan="3">В системе не занесено не одного учителя!</th>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'subjects'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-6 col-12">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed()"></button>
                    <h1>Предметы</h1>
                    <div v-if="forms.f_subject.response.type == 'danger'">
                        <div v-for="item in forms.f_subject.response.message" class="alert alert-danger" role="alert">
                            @{{ item[0] }}
                        </div>
                    </div>
                    <div class="input-group mb-3">

                        <input type="text"
                               class="form-control"
                               placeholder="Наименование предмета"
                               v-model="forms.f_subject.input.name">
                        <button class="btn btn-outline-secondary" type="button" v-on:click="buttonAddSubject">Создать</button>
                    </div>

                    <div v-if="forms.f_subject_edit.active" class="border border-danger border-3 rounded-3 p-1">
                        <h6>Редактирование предмета</h6>
                        <div class="input-group mb-3">
                            <input type="text"
                                   class="form-control"
                                   placeholder="Наименование предмета"
                                   v-model="forms.f_subject_edit.name">
                            <button class="btn btn-outline-secondary" type="button" v-on:click="buttonEditSubject">Редактировать</button>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Наименование предмета</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in subjects">
                            <th scope="row">@{{ item.id }}</th>
                            <td v-on:dblclick="dbbuttonEditSubject(item)">@{{ item.name }}</td>
                            <td>
                            </td>
                        </tr>
                        <tr v-if="subjects.length==0">
                            <th colspan="3">В системе не занесено не одного предмета!</th>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'groups'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-6 col-12 ">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed()"></button>
                    <h1>Группы</h1>
                    <div v-if="forms.f_group.response.type == 'danger'">
                        <div v-for="item in forms.f_group.response.message" class="alert alert-danger" role="alert">
                            @{{ item[0] }}
                        </div>
                    </div>
                    <div class="input-group mb-3">

                        <input type="text"
                               class="form-control"
                               placeholder="Наименование группы"
                               v-model="forms.f_group.input.name">
                        <button class="btn btn-outline-secondary" type="button" v-on:click="buttonAddGroup">Создать</button>
                    </div>

                    <div v-if="forms.f_group_edit.active" class="border border-danger border-3 rounded-3 p-1">
                        <h6>Редактирование Группы</h6>
                        <div class="input-group mb-3">
                            <input type="text"
                                   class="form-control"
                                   placeholder="Наименование предмета"
                                   v-model="forms.f_group_edit.name">
                            <button class="btn btn-outline-secondary" type="button" v-on:click="buttonEditGroup">Редактировать</button>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" title="Позиция">Поз</th>
                            <th scope="col">Наименование группы</th>
                            <th scope="col">Связанные предметы</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in groups">
                            <td scope="row">@{{ item.id }}</td>
                            <th><input type="number" v-on:change="setPositionGroup(item)" style="width: 50px;" v-model="item.position" min="0" class="form-control form-control-sm"></th>
                            <td v-on:dblclick="dbbuttonEditGroup(item)">@{{ item.name }}</td>
                            <td><button class="btn btn-outline-primary" v-on:click="buttonGroupSubjects(item.id)">Связанные предметы</button></td>
                        </tr>
                        <tr v-if="groups.length==0">
                            <th colspan="3">В системе не занесено не одного предмета!</th>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'teacher'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-8 col-12">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed('teachers')"></button>
                    <h2>Преподаватель / @{{ teacherSubject.fullname }}</h2>
                    <select class="form-select" v-on:change="buttonAddTeacherSubject(teacherSubject.teacher_id)" v-model="teacherSubject.subject_id">
                        <option value="">Выбирайте предмет, чтобы добавить для педагога</option>
                        <option v-for="item in teacherSubject.not_subjects" v-bind:value="item.id">@{{ item.name }}</option>
                    </select>
                    <p class="small">Нажмите на предмет, чтобы его удалить.
                        <br>
                        <span class="text-danger">Нельзя удалить предмет, если его использовали при создании расписания.</span>
                    </p>
                    <div class="list-group">
                        <button
                            type="button"
                            class="list-group-item list-group-item-action"
                            v-for="item in teacherSubject.subjects"
                            v-on:dblclick="buttonDeleteTeacherSubject(item.subject_id, item.teacher_id)">@{{ item.subject_name }}</button>
                    </div>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container" v-if="pages == 'timetable'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-6 col-12">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed()"></button>
                    <h2>Даты с расписанием</h2>
                    <div>
                        <label for="formFileLg" class="form-label">Дата для создания расписания</label>
                        <input class="form-control form-control-lg" id="formFileLg" type="date" v-on:change="buttonAddTimetable()" v-model="forms.f_timetable.input.timetable">
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Дата</th>
                            <th scope="col">Печать</th>
                            <th scope="col">Печать педагоги</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in timetables.data">
                                <td scope="row" v-on:click="openTimetable(item.timetable)">@{{ item.timetable_format + getNameDay(item.dayName) }}</td>
                                <td scope="row" oncha><button type="button" class="btn btn-primary btn-sm" v-on:click="openPrintTimetable(item.timetable)">Учащимся</button></td>
                                <td scope="row"><button type="button" class="btn btn-primary btn-sm" v-on:click="openPrintTeacherTimetable(item.timetable)">Педагогам</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg"></div>
            </div>
        </div>
        <div class="container-fluid" v-if="pages == 'timetable_open'">
            <div class="row mt-5">
                <div class="col-lg"></div>
                <div class="col-lg-8 col-12">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed('timetable')"></button>
                    <h3>Дата расписания: @{{ timetable_open.timetable }}</h3>
                    <div class="d-inline-block">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon2">Отборажение по группе</span>
                            <select class="form-select" v-model="selected_group">
                                <option value="-1">Не выбрана группа</option>
                                <option v-for="item in timetable_open.groups" v-bind:value="item.id">@{{ item.name }}</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" v-on:click="saveLessons(timetable_open.timetable)" class="btn btn-success">Сохранить</button>
                    <button type="button" v-on:click="openPrintTeacherTimetable(timetable_open.timetable, false)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTimetableOpen">Расписание педагогов</button>
                </div>
                <div class="col-lg"></div>
            </div>

            <div class="modal fade" id="modalTimetableOpen" tabindex="-1" aria-labelledby="modalTimetableOpen" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title h4" id="exampleModalFullscreenLabel">Расписание педагогов</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" v-if="timetable_teachers.teachers != {}">
                            <div class="printer w-100">
                                <div class="mt-3 tables">
                                    <table class="table-group p-0 m-0" style="
                        font-size: 12px;
                        min-width: 150px;
                        max-width: 150px" v-for="teacher in timetable_teachers.teachers">
                                        <tr><th colspan="4">@{{ teacher.fullname }}</th></tr>
                                        <tr v-for="numeric in [1,2,3,4,5,6,7,8]">
                                            <th style='width: 10px;'>@{{ numeric }}</th>
                                            <td>@{{ createTeacherDate(teacher.timetable, numeric) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col">
                    <div class='mt-2 w-100 overflow-auto' style="overflow-x: scroll; white-space: nowrap">
                        <div class="w-25 d-inline-block border" v-for="item in timetable_open.groups" v-if="selected_group == -1 || selected_group == item.id">
                            <table class="table">
                                <thead>
                                <tr><th colspan="2" class="text-center bg-info">@{{ item.name }}</th></tr>
                                <tr>
                                    <th scope="col" class="small text-center">Урок/Подгруппа</th>
                                    <th scope="col" class="small text-center">Предметы</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="tgts in item.tgts" v-bind:class="{ 'table-secondary' : (tgts.numeric % 2 == 0), 'table-danger' : !tgts.sub}">
                                    <th scope="row">@{{ tgts.numeric + (tgts.sub ? '/2' : '/1') }}</th>
                                    <td>
                                        <select class="form-select" v-model="tgts.teacher_subject_id" v-bind:style="{'background-color': tgts.color}" v-on:change="securityDoubleTeacher(tgts)">
                                            <option value="null">Не выбран предмет с педагогом</option>
                                            <option v-for="subject in getOpenGroups(item)" v-bind:value="subject.id">@{{ subject.subject_name + ' / ' + subject.teacher_fullname }}</option>
                                        </select>
                                        <select class="form-select" v-model="tgts.lesson_type" v-on:change="securityDoubleTeacher(tgts)">
                                            <option value="0">Лекция</option>
                                            <option value="1">Самостоятельная работа</option>
                                            <option value="2">Консультация</option>
                                            <option value="3">Экзамен</option>
                                        </select>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">№ кабинета</span>
                                            <input type="text" class="form-control" v-model="tgts.cabinet" v-bind:class="{ 'is-invalid' : tgts.cabinet_color}" v-on:change="securityDoubleTeacher()">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="printer" v-on:dblclick="closed('timetable')" v-if="pages == 'timetable_table'">
            <div style="text-align: right; font-size: 12px;">
                Утверждаю<br>
                Зам. по УР _________________ С.Г. Панова
            </div>
            <div style="text-align: center; font-size: 22px; font-weight: bold;">Расписание занятий на @{{ timetable_table.timetable_format + getNameDay(timetable_table.dayName) }}</div>
            <div class="mt-3 tables">
                <table class="table-group p-0 m-0" style="
                    font-size: 8px;
                    min-width: calc(100% / 7);
                    max-width: calc(100% / 7)" v-for="group in timetable_table.groups">
                    <tr><th colspan="4" style="font-size: 12px;">@{{ group.name }}</th></tr>
                    <tr v-for="numeric in [1,2,3,4,5,6,7,8]">
                        <th style='width: 10px;'>@{{ numeric }}</th>
                        <td v-for="item in createDataGroup(group.tgts, numeric)">@{{ item }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="printer" v-on:dblclick="closed('timetable')" v-if="pages == 'timetable_teachers'">
            <h4>Расписание на @{{ timetable_teachers.timetable }}</h4>
            <div class="mt-3 tables">
                <table class="table-group p-0 m-0" style="
                    font-size: 8px;
                    min-width: calc(100% / 7);
                    max-width: calc(100% / 7)" v-for="teacher in timetable_teachers.teachers">
                    <tr><th colspan="4" style="font-size: 12px;">@{{ teacher.fullname }}</th></tr>
                    <tr v-for="numeric in [1,2,3,4,5,6,7,8]">
                        <th style='width: 10px;'>@{{ numeric }}</th>
                        <td>@{{ createTeacherDate(teacher.timetable, numeric) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="container" v-if="pages == 'group_related'">
            <div class="row mt-5">
                <div class="col"></div>
                <div class="col-lg-6 col-10">
                    <button type="button" class="btn-close" aria-label="Close" v-on:click="closed()"></button>
                    <h1>Группа: @{{ group_related.name }}</h1>
                    <select class="form-select" v-on:change="buttonAddGroupSubject(group_related.group_id)" v-model="group_related.ts_id">
                        <option value="">Выбирайте предмет, чтобы добавить для педагога</option>
                        <option v-for="item in group_related.not_teacher_subjects" v-bind:value="item.id">@{{ item.subject_name + '/' + item.teacher_fullname }}</option>
                    </select>
                    <p class="small">Нажмите на предмет, чтобы его удалить.
                        <br>
                        <span class="text-danger">Нельзя удалить предмет, если его использовали при создании расписания.</span>
                    </p>

                    <div class="list-group">
                        <button
                            type="button"
                            class="list-group-item list-group-item-action"
                            v-for="item in group_related.teacher_subjects"
                            v-on:dblclick="buttonDeleteGroupSubject(group_related.group_id, item.id)">@{{ item.subject_name + '/' + item.teacher_fullname }}</button>
                    </div>
                </div>
                <div class="col"></div>
            </div>
        </div>

        <div class="download" v-if="download">
            Загрузка...
        </div>
    </div>
</body>
</html>
