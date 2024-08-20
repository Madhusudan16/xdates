<!-- Modal -->
    <div class="modal fade addNewUser" data-backdrop="static" data-keyboard="false" id="addNewUserModal" role="dialog" aria-labelledby="addNewUser">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="xfulldetail-disabled hide"><img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading.."></div>
                <div class="row">
                    <div class="col-md-6 addNewUser-left">
                        <div class="modal-header">
                            <span class="x-error-msg visible-xs-block hide"></span>
                            <span class="x-success-msg visible-xs-block  hide"></span>
                            <div class="button-right">
                            	<span class="x-error-msg hidden-xs hide"></span>
                            	<span class="x-success-msg hidden-xs hide"></span>
                            
                                <button type="button" id="save_xdate_btn" class="btn btn-success edit-mode">Save</button>
                                <button type="button" id="edit_xdate_btn" class="btn btn-success view-mode">Edit</button>
                            </div>
                            <h4 class="modal-title">Details</h4>
                        </div>
                        <div class="modal-body">
                            <form id="add_edit_xdate_form" autocomplete="off"  name="add_edit_xdate_form">
                            <input type="hidden" value="" name="xaction" id="xaction" />
                            <input type="hidden" value="" name="current_xdate_id" id="current_xdate_id" />
                            <div class="detail-container">
                                <div class="xdetail-disabled hide"><img class="loading-image" src="{{asset('assets/images/loader-small.gif')}}" alt="loading.."></div>
                                <div class="row">
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="xdate">X-Date<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-xdate-text>09/01</span></div>
                                            <div data-xdate-value class="input-group date edit-mode x_form_date x-date-date xdate-picker" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2">
                                                <input class="form-control" size="16" readonly type="text" name="xdate" id="xdate" value="">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                            <input type="hidden" name="xdate_org" id="xdate_org" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="xname">Name<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-xname-text>X Name</span></div>
                                            <div class="edit-mode"><input type="text" class="value" name="xname" id="xname" /></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="line">Line<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-line-text>Line 1</span></div>
                                            <div class="edit-mode">
                                                <select id="line" name="line" class="select2" >
                                                    <option value="">--Select One--</option>

                                                    @foreach ($linesList as $item)
                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                        @if(strtolower($item->name) == 'commercial')
                                                            <?php $defaultLine = $item->id; $defaultLineText = $item->name; ?>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="policy_type">Policy Type<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-policytype-text>Policy Type 1</span></div>
                                            <div class="edit-mode">
                                            <select id="policy_type" name="policy_type" class="select2" >
                                                <option value="">--Select One--</option>
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="industry">Industry<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-industry-text>Industry 1</span></div>
                                            <div class="edit-mode">
                                                <select name="industry" id="industry" class="select2" >
                                                    <option value="">--Select One--</option>
                                                    @foreach ($industryList as $item)
                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="contact">Contact<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-contact-text>Rodgers Group</span></div>
                                            <div class="edit-mode">
                                                <input type="text" name="contact" id="contact" class="value" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="producer">Producer<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-producer-text>Producer 1</span></div>
                                            <div class="edit-mode">
                                            <select name="producer" id="producer" class="select2">
                                                <option value="">--Select One--</option>
                                                @foreach ($producers as $item)
												@if($user->user_type > 2)
													@if($item->id != $user->id)
														@continue;
													@endif
												@endif


                                                @if($item->id == $user->id)
                                                <option value="{{$item->id}}" selected="selected">
                                                    {{$item->name}} (you)
                                                @else
                                                 <option value="{{$item->id}}">
                                                    {{$item->name}}
                                                @endif
                                                </option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="phone">Phone</label>
                                            <div class="view-mode"><span class="value" data-phone-text>Rodgers Group</span></div>
                                            <div class="edit-mode">
                                                <input type="text" id="phone" name="phone" class="value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="city">City<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-city-text>Rodgers Group</span></div>
                                            <div class="edit-mode">
                                                <input type="text" id="city" name="city" class="value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="state">State<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-state-text>State 1</span></div>
                                            <div class="edit-mode">
                                                <select  id="state" name="state" class="select2">
                                                    <option value="">--Select One--</option>
                                                    @foreach ($stateList as $item)
                                                        <option value="{{$item->state_code}}">{{$item->state_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="website">Website</label>
                                            <div class="view-mode"><span class="value" data-website-text>Website</span></div>
                                            <div class="edit-mode">
                                                <input type="text" id="website" name="website" class="value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="email">Email</label>
                                            <div class="view-mode"><span class="value" data-email-text>Rodgers Group</span></div>
                                            <div class="edit-mode">
                                                <input type="text" id="email" name="email" class="value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="status">Status<span class="streak-sign">*</span></label>
                                            <div class="view-mode"><span class="value" data-status-text>Rodgers Group</span></div>
                                            <div class="edit-mode">
                                            <select  id="status" name="status" class="select2">
                                                <option value="">--Select One--</option>
                                                @foreach ($allStatus as $key=>$item)
                                                    <option value="{{$key}}">{{$item}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 detail-list">
                                        <div class="detail-field">
                                            <label for="follow_up_date">Follow-up<span class="streak-sign">*</span></label>
                                             <div class="view-mode"><span class="value" data-followupdate-text>09/12</span> </div>
                                            <!-- <span class="value">Rodgers Group</span> -->
                                            <div class="input-group date edit-mode x_form_date x-date-date fdate-picker" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="mm/dd/yyyy">
                                                <input class="form-control"  id="follow_up_date" name="follow_up_date" size="16" type="text" value="" readonly>
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 addNewUser-right">
                        <div class="modal-header">
                            <div class="button-right">
                                <button type="button" id="request_update" class="btn btn-success view-mode">Request Update</button>
                                <button type="button" id="add_note_btn" class="btn btn-success">Add Note</button>
                                <button type="button" class="btn btn-primary btn-close-primary" data-dismiss="modal">
                                    <img src="{{asset('assets/images/close_x.png')}}" alt="" />
                                </button>
                            </div>
                            <h4 class="modal-title">Notes</h4>
                        </div>
                        <div class="modal-body notes-modal-body">
                            <div class="scrollbox notes-scrollbox">
                                <div class="add-note-container hide">
                                    <form id="add_note_form" id="add_note_form">
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <textarea  id="notes_txt" name="notes_txt" rows="1" cols="40"></textarea>
                                            </div>
                                            <div class="col-xs-3 note-save-btn-box">
                                                <button type="button" id="save_note_btn" class="btn btn-primary text-uppercase" name="button"><strong>Save</strong></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                               <div class="no-notes-content text-center hide"><i>No notes</i></div>
                               <div class="notes-loader-content hide text-center"><strong>Loading Notes...</strong></div>
                               <div id="all-notes-list">
                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="note-container">
                                            <div class="note-header">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img class="media-object" src="{{asset('assets/images/pic-46.jpg')}}" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h6>Byron Winters</h6>
                                                        <div class="note-date-time">
                                                            <span>08/06/2013</span>
                                                            <span>9:47am</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="note-body">
                                                <p>
                                                    Spoke with Wes. He said they are happy with their current agent and declined a 2nd opinion.
                                                </p>
                                            </div>
                                        </div>

                               </div>
                            </div>
                           <div class="skiptonext">
                                <button type="button" id="skip_next_btn" disabled class="btn  btn-success">
                                    skip to next x-date
                                </button>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade small-xpopup" id="no_next_date_found" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Skip to next x-date</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>System could not find next x-date in comparison to current x-date.</p>
                            <div class="text-right">
                <form>
                <input type="hidden" name="policyId" id="policyId">
                                    <button type="button" class="btn btn-danger" onclick="hideXModal('no_next_date_found');">Close</button>
                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade small-xpopup" id="x_save_discard_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog account-model" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Do you want to save or discard changes?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <button type="button" class="btn btn-success" onclick="SaveXformChanges();">Save</button>
                                <button type="button" class="btn btn-danger" onclick="DiscardXformChanges();">Discard</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
