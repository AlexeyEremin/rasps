/*
 * Разработка: Еремин А.А.
 * Сайт: https://eremin.tech/
 * Версия 1.2
 * От 22-02-2021 v1.1
 * От 27-02-2021 v1.2
 * От 28-02-2021 v1.3
 * От 28-02-2021 v1.3.1
 * От 28-02-2021 v1.3.2
 * От 01-03-2021 v1.3.3
 * От 19-03-2021 v1.3.4
 */

async function response(host, data = {}) {
    data = {
        bearer: localStorage.getItem('token'),
        method: 'GET',
        body: null,
        headers: {},
        ...data
    };
    let res = await fetch(`/api/${host}`, {
        method: data.method,
        headers: {
            'Content-Type' : 'application/json',
            'Authorization': 'Bearer ' + data.bearer,
            ...data.headers
        },
        body: data.body
    })
    let resJson = await res.json()
    return {status: res.status, json: resJson}
}

let app = new Vue({
    el: "#app",
    data: {
        download: false,
        pages: "login",
        token: null,
        forms: {
            f_login: {
                input: {
                    login: '',
                    password: '',
                    stay: false
                },
                response: {
                    type: '',
                    message: []
                }
            },
            f_teacher: {
                input: {
                    fullname: '',
                    color: '#eb4444'
                },
                response: {
                    type: '',
                    message: []
                }
            },
            f_subject: {
                input: {
                    name: ''
                },
                response: {
                    type: '',
                    message: []
                }
            },
            f_group: {
                input: {
                    name: ''
                },
                response: {
                    type: '',
                    message: []
                }
            },
            f_timetable: {
                input: {
                    timetable: ''
                }
            },
            f_teacher_edit: {
                fullname: '',
                color: '',
                active: false,
                id: null
            },
            f_subject_edit: {
                name: '',
                active: false,
                id: null
            },
            f_group_edit: {
                name: '',
                active: false,
                id: null
            }
        },
        teachers: [],
        teacherSubject: {
            fullname: '',
            teacher_id: null,
            subjects: [],
            not_subjects: [],
            subject_id: '',
        },
        group_related: {
            name: '',
            ts_id: null,
            group_id: null,
            teacher_subjects: [],
            not_teacher_subjects: [],
        },
        subjects: [],
        groups: [],
        timetables: [],
        timetable_open: [],
        timetable_open_lessons: [],
        timetable_table: [],
        timetable_teachers: [],
        modal: {
            active: false,
            name: ''
        },
        selected_group: -1,
    },
    methods: {
        clearData() {
            // Очистка данных из всех параметров
            this.download = false;
            this.forms = {
                f_login: {
                    input: {
                        login: '',
                            password: '',
                            stay: false
                    },
                    response: {
                        type: '',
                            message: []
                    }
                },
                f_teacher: {
                    input: {
                        fullname: '',
                            color: '#eb4444'
                    },
                    response: {
                        type: '',
                            message: []
                    }
                },
                f_subject: {
                    input: {
                        name: ''
                    },
                    response: {
                        type: '',
                            message: []
                    }
                },
                f_group: {
                    input: {
                        name: ''
                    },
                    response: {
                        type: '',
                            message: []
                    }
                },
                f_timetable: {
                    input: {
                        timetable: ''
                    }
                },
                f_teacher_edit: {
                    fullname: '',
                        color: '',
                        active: false,
                        id: null
                },
                f_subject_edit: {
                    name: '',
                    active: false,
                    id: null
                },
                f_group_edit: {
                    name: '',
                    active: false,
                    id: null
                }
            };
            this.teachers = [];
            this.teacherSubject = {
                fullname: '',
                teacher_id: null,
                subjects: [],
                not_subjects: [],
                subject_id: '',
            };
            this.subjects =
            this.groups =
            this.timetables =
            this.timetable_open =
            this.timetable_open_lessons =
            this.timetable_table =
            this.timetable_teachers =[];
            this.modal = { active: false, name: '' };
            this.selected_group = -1;
        },
        async buttonLogin() {
            this.download = true;
            let data = {
                method: 'POST',
                body: JSON.stringify(this.forms.f_login.input)
            };
            let responses = await response('login', data);
            this.download = false;
            this.forms.f_login.response.type = (responses.status == 201 ? 'success' : 'danger');
            if(responses.status != 201)
                this.forms.f_login.response.message = responses.json;
            else {
                localStorage.setItem('token', responses.json.token);
                this.auth();
            }
        },
        auth(status = null) {
            if(status) localStorage.clear();
            this.token = localStorage.getItem('token');
            this.download = false;
            if(!this.token)  {
                this.pages = 'login';
                return;
            }

            this.pages = (this.pages == 'login' ? 'main' : this.pages);

        },
        async buttonTeachers() {
            this.clearData();
            this.download = true;
            let responses = await response('teachers');
            this.download = false;

            if(responses.status == 401) return this.auth(401);
            this.teachers = responses.json;
            this.pages = 'teachers';
        },
        async buttonAddTeacher() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_teacher.input)
            }
            let responses = await response('teachers', data);
            this.download = false;
            if(responses.status == 400) {
                this.forms.f_teacher.response.type = 'danger';
                this.forms.f_teacher.response.message = responses.json;
                return;
            }
            this.clearData();
            this.buttonTeachers();
        },
        async buttonFirstTeacher(id) {
            this.download = true;
            let responses = await response(`teachers/${id}`);
            this.download = false;
            if(responses.status == 401)
                return this.auth(401);

            this.teacherSubject.fullname = responses.json.fullname;
            this.teacherSubject.teacher_id = id;
            this.teacherSubject.subjects = responses.json.subjects;
            this.teacherSubject.not_subjects = responses.json.not_subjects;
            this.pages = 'teacher';
        },
        async buttonAddTeacherSubject(id) {
            this.download = true;
            let subject_id = this.teacherSubject.subject_id;
            if(subject_id == '') return;
            let data = {
                method: "POST",
                body: JSON.stringify({
                    subject_id: subject_id,
                    teacher_id: id
                })
            };
            let responses = await response('teachers/subject', data);
            this.download = false;
            this.buttonFirstTeacher(id);
        },
        async buttonDeleteTeacherSubject(subject_id, teacher_id) {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify({
                    subject_id: subject_id,
                    teacher_id: teacher_id
                })
            };
            let responses = await response('teachers/subject/delete', data);
            this.download = false;
            this.buttonFirstTeacher(teacher_id);
        },
        async buttonSubject() {
            this.clearData();
            this.download = true;
            let responses = await response('subjects');
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.subjects = responses.json;
            this.pages = 'subjects';
        },
        async buttonAddSubject() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_subject.input)
            };
            let responses = await response('subjects', data);
            this.download = false;
            if(responses.status == 400) {
                this.forms.f_subject.response.type = 'danger';
                this.forms.f_subject.response.message = responses.json;
                return;
            }
            this.clearData();
            this.buttonSubject();
        },
        async buttonGroup() {
            this.download = true;
            let responses = await response('groups');
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.clearData();
            this.groups = responses.json;
            this.pages = 'groups';
        },
        async buttonAddGroup() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_group.input)
            };
            let responses = await response('groups', data);
            this.download = false;
            if(responses.status == 400) {
                this.forms.f_group.response.type = 'danger';
                this.forms.f_group.response.message = responses.json;
                return;
            }
            this.buttonGroup();
        },
        async buttonTimetable() {
            this.download = true;
            let responses = await response('timetable');
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.clearData();
            this.timetables = responses.json;
            this.pages = 'timetable';
        },
        async buttonAddTimetable() {
            this.download = true;
            let data = {
                method: 'POST',
                body: JSON.stringify(this.forms.f_timetable.input)
            };
            let responses = await response('timetable', data);
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.buttonTimetable();
        },
        async openTimetable(date) {
            this.download = true;
            let responses = await response(`timetable/${date}`);
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.clearData();
            this.timetable_open = responses.json;
            this.pages = 'timetable_open';
            this.securityDoubleTeacher();
        },
        getOpenGroups(item) {
            let arrayTSID = [];
            item.ts_group.forEach(t => {
                arrayTSID.push(t.ts_id);
            });
            return this.timetable_open.subjects.filter(t => (arrayTSID.findIndex(b => b == t.id) != -1)).sort((a,b) => {
                let aname = a.subject_name.toLowerCase(), bname = b.subject_name.toLowerCase();
                if(aname < bname) return -1;
                if(aname > bname) return 1;
            });
        },
        async openPrintTimetable(date) {
            this.download = true;
            let responses = await response(`timetable/${date}`);
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.timetable_table = responses.json;
            this.pages = 'timetable_table';
        },
        createDataGroup(tgts, numeric) {
            let nameSubject = '';
            let cabient = '';

            // Получаем группы
            let group_0 = tgts.find(t => t.numeric == numeric && t.sub == 0);
            let group_1 = tgts.find(t => t.numeric == numeric && t.sub == 1);

            if(group_0 == undefined && group_1 == undefined) return ['', ''];
            // Получаем педагогов и предметы
            let group_0_teacher = this.timetable_table.subjects.find(s => s.id == group_0.teacher_subject_id);
            let group_1_teacher = this.timetable_table.subjects.find(s => s.id == group_1.teacher_subject_id);

            if(group_0_teacher == undefined && group_1_teacher == undefined) return ['', ''];
            if(group_0_teacher != undefined && group_1_teacher == undefined) {
                nameSubject = group_0_teacher.subject_name + this.typeLesson(group_0.lesson_type);
                cabient = '' + (group_0.cabinet != null ? group_0.cabinet : '');
            }
            else if(group_0_teacher == undefined && group_1_teacher != undefined) {
                nameSubject = group_1_teacher.subject_name + this.typeLesson(group_1.lesson_type);
                cabient = '/' + (group_1.cabinet != null ? group_1.cabinet : '');
            }
            else {
                nameSubject = (group_0_teacher.subject_id != null && group_1_teacher.subject_id != null ? group_0_teacher.subject_name  + this.typeLesson(group_0.lesson_type) + '/' + group_1_teacher.subject_name  + this.typeLesson(group_1.lesson_type) : nameSubject);
                nameSubject = (group_0_teacher.subject_id == group_1_teacher.subject_id && group_0_teacher.subject_id != null ? group_0_teacher.subject_name  + this.typeLesson(group_0.lesson_type) : nameSubject);
                cabient = '' + (group_0.cabinet != null ? group_0.cabinet : '');
                cabient += '/' + (group_1.cabinet != null ? group_1.cabinet : '');
            }

            return [nameSubject, cabient];
        },
        typeLesson(type) {
            return (type == 0 ? '' :
                        (type == 1 ? ' (с/р)' :
                            (type == 2 ? ' (конс.)' :
                                ' (экзамен)')));
        },
        async openPrintTeacherTimetable(date, table = true) {
            this.download = true;
            let responses = await response(`teachers/timetable/${date}`);
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.timetable_teachers = responses.json;
            if(table) {
                this.timetable_teachers.teachers = this.timetable_teachers.teachers.filter(obj => obj.timetable.length > 0);
                this.pages = 'timetable_teachers';
            }
        },
        createTeacherDate(teacher, numeric) {
            let timetable = teacher.filter(t => t.numeric == numeric);
            if(timetable == undefined) return '';
            let text = '';
            timetable.forEach(t => {
                let name_group = this.timetable_teachers.groups.find(g => g.id == t.group_id).name + this.typeLesson(t.lesson_type);
                text += (text == '' ? name_group : '/' + name_group);
            });
            return text;
        },
        async saveLessons(date) {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.timetable_open.groups)
            };
            let responses = await response(`timetable/save`, data);
            this.download = false;
            if(responses.status == 401) return this.auth(401);
            this.openTimetable(date);
        },
        async securityDoubleTeacher(tgts = null) {

            if(tgts != null)
                response('timetable/first/save', {
                    method: "POST",
                    body: JSON.stringify(tgts)
                });

            this.timetable_open.groups.forEach(group => {
               group.tgts.forEach(tgts => {
                  tgts.color = '#ffffff';
                  tgts.cabinet_color = false;
               });
            });

            this.timetable_open.groups.forEach(el => {
                this.timetable_open.groups.forEach(el1 => {
                    el.tgts.forEach(t => {
                        el1.tgts.forEach(t1 => {
                            if(t.id == t1.id) return;
                            if(t.numeric != t1.numeric) return;
                            if(t.teacher_subject_id != null && t1.teacher_subject_id != null
                                && t.teacher_subject_id != 'null' && t1.teacher_subject_id != 'null') {
                                let t_teacher_id = this.timetable_open.subjects.find(teacher => teacher.id == t.teacher_subject_id);
                                let t1_teacher_id = this.timetable_open.subjects.find(teacher => teacher.id == t1.teacher_subject_id);
                                if(t_teacher_id.teacher_id == t1_teacher_id.teacher_id) t1.color = t.color = t_teacher_id.color;
                            }
                            if(t.cabinet != null && t1.cabinet != null)
                                if(t.cabinet == t1.cabinet)
                                    t.cabinet_color = t1.cabinet_color = true;
                        });
                    });
                });
            });
        },
        dbbuttonEditTeacher(item) {
            this.forms.f_teacher_edit.color = item.color;
            this.forms.f_teacher_edit.id = item.id;
            this.forms.f_teacher_edit.fullname = item.fullname;
            this.forms.f_teacher_edit.active = true;
        },
        async buttonEditTeacher() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_teacher_edit)
            };
            let responses = await response(`teacher/${this.forms.f_teacher_edit.id}`, data);
            this.download = false;
            this.forms.f_teacher_edit.active = false;
            this.buttonTeachers();
        },
        dbbuttonEditSubject(item) {
            this.forms.f_subject_edit.id = item.id;
            this.forms.f_subject_edit.name = item.name;
            this.forms.f_subject_edit.active = true;
        },
        async buttonEditSubject() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_subject_edit)
            };
            await response(`subject/${this.forms.f_subject_edit.id}`, data)
            this.download = false;
            this.forms.f_subject_edit.active = false;
            this.buttonSubject();
        },
        dbbuttonEditGroup(item) {
            this.forms.f_group_edit.id = item.id;
            this.forms.f_group_edit.name = item.name;
            this.forms.f_group_edit.active = true;
        },
        async buttonEditGroup() {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify(this.forms.f_group_edit)
            };
            await response(`group/${this.forms.f_group_edit.id}`, data);
            this.download = false;
            this.forms.f_group_edit.active = false;
            this.buttonGroup();
        },
        async buttonGroupSubjects(group_id) {
            this.download = true;
            let responses = await response(`groups/related/${group_id}`);
            this.download = false;
            this.group_related.group_id = group_id;
            this.group_related.name = responses.json.name;
            this.group_related.not_teacher_subjects = responses.json.not_teacher_subjects.sort((a,b) => {
                let aname = a.subject_name.toLowerCase(), bname = b.subject_name.toLowerCase();
                if(aname < bname) return -1;
                if(aname > bname) return 1;
            });
            this.group_related.teacher_subjects = responses.json.teacher_subjects.sort((a,b) => {
                let aname = a.subject_name.toLowerCase(), bname = b.subject_name.toLowerCase();
                if(aname < bname) return -1;
                if(aname > bname) return 1;
            });
            this.group_related.ts_id = '';
            this.pages = 'group_related';

        },
        async buttonAddGroupSubject(group_id) {
            this.download = true;
            let data = {
                method: "POST",
                body: JSON.stringify({
                    group_id: group_id,
                    ts_id: this.group_related.ts_id
                })
            }
            await response('groups/related', data);
            this.download = false;
            this.buttonGroupSubjects(group_id);
        },
        async buttonDeleteGroupSubject(group_id, related_id) {
            this.download = true;
            await response(`groups/related/${group_id}/${related_id}`, {method: "DELETE"});
            this.download = false;
            this.buttonGroupSubjects(group_id);
        },
        async setPositionGroup(group) {
            this.download = true;
            await response(`group/${group.id}/position/${group.position}`, {method: "POST"});
            this.download = false;
            this.buttonGroup();
        },
        getNameDay(name_id) {
            let name = '';
            switch(name_id) {
                case 1: name = "Понедельник"; break;
                case 2: name = "Вторник"; break;
                case 3: name = "Среда"; break;
                case 4: name = "Четверг"; break;
                case 5: name = "Пятница"; break;
                case 6: name = "Суббота"; break;
                case 7: name = "Воскресенье"; break;
            }
            return '(' + name + ')';
        },
        closed(name = 'none') {
            this.pages = (name == 'none' ? 'main' : name);
        }
    },
    mounted() {
        this.auth();
    }
});
